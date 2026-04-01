const APIMEDIAURL = `http://127.0.0.1:8000/media-re-search/upload-posts.php`;
const LOGO_URL = "/assets/img/logo.svg";
const DARK_LOGO_URL = "/assets/img/logo.svg";
const LIGHT_LOGO_URL = "/assets/img/logo-dark.svg";

const OPTIMIZATION_CONFIG = {
    MAX_CONCURRENT_PROCESSING: 5, // Chỉ xử lý 5 ảnh cùng lúc
    CHUNK_SIZE: 15, // Xử lý theo batch 5 ảnh
    IMAGE_QUALITY: 1.0, // Giảm quality để giảm size base64
    MAX_IMAGE_WIDTH: 1950, // Resize ảnh lớn xuống
    PROCESSING_DELAY: 50, // Delay giữa các batch (ms)
};

CKEDITOR.timestamp = 'v' + new Date().getTime();
CKEDITOR.editorConfig = function (config) {
    config.language = "vi";
    config.height = 500;
    config.filebrowserUploadMethod = "form";
    config.extraPlugins =
        "uploadimage,image2,clipboard,colorbutton,font,justify,wordcount,pastefromword,autogrow,preview,mediaembed,imagerotate,devchinh_quote";
    config.removePlugins = "image,autocorrect";
    config.image2_captionedClass = "image-captioned";
    config.image2_defaultCaption = "Ghi chú ảnh...";
    config.uploadImage = {
        base64imageUpload: true,
    };

    config.disableNativeSpellChecker = false;
    config.autoParagraph = false;

    config.toolbar = [
        {
            name: "document",
            items: ["Source", "Preview", "-", "Maximize"],
        },
        {
            name: "clipboard",
            items: [
                "Cut",
                "Copy",
                "Paste",
                "PasteText",
                "PasteFromWord",
                "-",
                "Undo",
                "Redo",
            ],
        },
        {
            name: "styles",
            items: ["Format", "Font", "FontSize"],
        },
        {
            name: "colors",
            items: ["TextColor", "BGColor"],
        },
        {
            name: "basicstyles",
            items: [
                "Bold",
                "Italic",
                "Underline",
                "Strike",
                "-",
                "RemoveFormat",
            ],
        },
        {
            name: "paragraph",
            items: [
                "NumberedList",
                "BulletedList",
                "-",
                "Outdent",
                "Indent",
                "-",
                "Blockquote",
                "JustifyLeft",
                "JustifyCenter",
                "JustifyRight",
                "JustifyBlock",
            ],
        },
        {
            name: "links",
            items: ["Link", "Unlink", "Anchor"],
        },
        {
            name: "insert",
            items: [
                "Image",
                "ImageRotate",
                "Table",
                "HorizontalRule",
                "SpecialChar",
                "MediaEmbed",
            ],
        },
        {
            name: "devchinh_quote",
            items: ["devchinh_quote"],
        },
    ];

    // config.extraAllowedContent =
    //     "img[style,data-temp-marker,data-has-logo,data-original-width,data-original-height]{*},i(*)[*]";
    config.allowedContent = true;
};


CKEDITOR.on('dialogDefinition', function (ev) {
    var dialogName = ev.data.name;
    var dialogDefinition = ev.data.definition;

    if (dialogName === 'table') {
        dialogDefinition.onShow = function () {

            this.setValueOf('info', 'txtRows', 3);
            this.setValueOf('info', 'txtCols', 2);
            this.setValueOf('info', 'selHeaders', 'row');
        };
    }
});


class OptimizedImageLogoManager {
    constructor(lightLogoUrl, darkLogoUrl) {
        this.lightLogoUrl = lightLogoUrl;
        this.darkLogoUrl = darkLogoUrl;
        this.lightLogoCache = null;
        this.darkLogoCache = null;
        this.processingQueue = [];
        this.isProcessing = false;
        this.loadLogos();
    }

    async loadLogos() {
        try {
            // Load logo sáng
            const lightLogo = new Image();
            lightLogo.crossOrigin = "Anonymous";
            await new Promise((resolve, reject) => {
                lightLogo.onload = resolve;
                lightLogo.onerror = reject;
                lightLogo.src = this.lightLogoUrl;
            });
            this.lightLogoCache = lightLogo;

            // Load logo tối
            const darkLogo = new Image();
            darkLogo.crossOrigin = "Anonymous";
            await new Promise((resolve, reject) => {
                darkLogo.onload = resolve;
                darkLogo.onerror = reject;
                darkLogo.src = this.darkLogoUrl;
            });
            this.darkLogoCache = darkLogo;
        } catch (error) {
            console.warn("Không thể preload logo:", error);
        }
    }

    analyzeImageBrightness(img) {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        // Dùng đúng kích thước thật của ảnh
        const canvasWidth = img.naturalWidth || img.width;
        const canvasHeight = img.naturalHeight || img.height;

        canvas.width = canvasWidth;
        canvas.height = canvasHeight;

        ctx.drawImage(img, 0, 0, canvasWidth, canvasHeight);

        // Xác định vùng logo (góc phải dưới)
        const logoScale = 0.08;
        const logoWidth = Math.round(canvasWidth * logoScale);
        const logoHeight = Math.round(logoWidth * (img.height / img.width)); // cùng tỉ lệ
        const padding = 4;

        const logoX = Math.max(
            0,
            Math.floor(canvasWidth - logoWidth - padding)
        );
        const logoY = Math.max(
            0,
            Math.floor(canvasHeight - logoHeight - padding)
        );

        try {
            const imageData = ctx.getImageData(
                logoX,
                logoY,
                logoWidth,
                logoHeight
            );
            const data = imageData.data;
            let totalBrightness = 0;

            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                const brightness = 0.299 * r + 0.587 * g + 0.114 * b;
                totalBrightness += brightness;
            }

            const avgBrightness = totalBrightness / (data.length / 4);
            return avgBrightness; // 0 (tối) → 255 (sáng)
        } catch (error) {
            console.warn("Không thể phân tích độ sáng:", error);
            return 128;
        }
    }

    selectApproprirateLogo(brightness) {
        // Ngưỡng: dưới 100 là ảnh tối (dùng logo sáng), trên 100 là ảnh sáng (dùng logo tối)
        const threshold = 100;

        if (brightness < threshold) {
            // Ảnh tối -> dùng logo sáng
            return this.lightLogoCache;
        } else {
            // Ảnh sáng -> dùng logo tối
            return this.darkLogoCache;
        }
    }

    async processSingleImage(imageUrl) {
        return new Promise(async (resolve, reject) => {
            try {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                const img = new Image();
                img.crossOrigin = "Anonymous";

                img.onload = async () => {
                    try {
                        // Resize nếu cần
                        const { img: processedImg, scale } =
                            this.resizeImageIfNeeded(img);

                        // Đợi processed image load xong nếu đã resize
                        if (scale < 1) {
                            await new Promise((resolveResize) => {
                                processedImg.onload = resolveResize;
                            });
                        }

                        const finalImg = scale < 1 ? processedImg : img;

                        // PHÂN TÍCH ĐỘ SÁNG VÀ CHỌN LOGO
                        const brightness =
                            this.analyzeImageBrightness(finalImg);
                        const selectedLogo =
                            this.selectApproprirateLogo(brightness);

                        console.log(
                            `Độ sáng ảnh: ${brightness.toFixed(1)}, Logo: ${brightness < 100 ? "Sáng" : "Tối"
                            }`
                        );

                        canvas.width = finalImg.width;
                        canvas.height = finalImg.height;

                        // Vẽ ảnh gốc
                        ctx.drawImage(finalImg, 0, 0);

                        // Vẽ logo đã chọn
                        if (selectedLogo) {
                            await this.drawLogoOnCanvas(
                                ctx,
                                canvas.width,
                                canvas.height,
                                selectedLogo
                            );
                        }

                        const processedImageData = canvas.toDataURL(
                            "image/jpeg",
                            OPTIMIZATION_CONFIG.IMAGE_QUALITY
                        );

                        resolve({
                            dataUrl: processedImageData,
                            originalWidth: img.width,
                            originalHeight: img.height,
                            compressed: scale < 1,
                            brightness: brightness, // Thêm thông tin độ sáng
                            logoType: brightness < 100 ? "light" : "dark", // Thêm thông tin loại logo
                        });
                    } catch (innerError) {
                        reject(innerError);
                    }
                };

                img.onerror = () => {
                    reject(new Error("Không tải được ảnh"));
                };

                img.src = imageUrl.startsWith("data:image")
                    ? imageUrl
                    : imageUrl;
            } catch (error) {
                reject(error);
            }
        });
    }

    async drawLogoOnCanvas(ctx, canvasWidth, canvasHeight, logoImage) {
        if (!logoImage) return;

        const logoScale = 0.08;
        const logoWidth = Math.round(canvasWidth * logoScale);
        const logoHeight = Math.round(
            (logoImage.height / logoImage.width) * logoWidth
        );

        const padding = 4;
        const logoX = canvasWidth - logoWidth - padding;
        const logoY = canvasHeight - logoHeight - 3;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = "high";

        // ctx.shadowColor = "rgba(0, 0, 0, 0.3)";
        // ctx.shadowBlur = 2;
        // ctx.shadowOffsetX = 1;
        // ctx.shadowOffsetY = 1;

        ctx.drawImage(logoImage, logoX, logoY, logoWidth, logoHeight);

        // ctx.shadowColor = "transparent";
        // ctx.shadowBlur = 0;
        // ctx.shadowOffsetX = 0;
        // ctx.shadowOffsetY = 0;
    }

    resizeImageIfNeeded(img, maxWidth = OPTIMIZATION_CONFIG.MAX_IMAGE_WIDTH) {
        if (img.width <= maxWidth) return { img, scale: 1 };

        const scale = maxWidth / img.width;
        const newWidth = Math.floor(img.width * scale);
        const newHeight = Math.floor(img.height * scale);

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = newWidth;
        canvas.height = newHeight;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = "high";

        ctx.drawImage(img, 0, 0, newWidth, newHeight);

        const resizedImg = new Image();
        resizedImg.src = canvas.toDataURL(
            "image/jpeg",
            OPTIMIZATION_CONFIG.IMAGE_QUALITY
        );

        return { img: resizedImg, scale };
    }
}

const optimizedLogoManager = new OptimizedImageLogoManager(
    LIGHT_LOGO_URL,
    DARK_LOGO_URL
);

class ProgressManager {
    constructor() {
        this.activeNotifications = new Map();
    }

    showProgress(id, message, current = 0, total = 0) {
        let notification = this.activeNotifications.get(id);

        if (!notification) {
            notification = document.createElement("div");
            notification.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                padding: 15px 20px;
                background: #2196F3;
                color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                font-family: -apple-system, BlinkMacSystemFont, sans-serif;
                font-size: 14px;
                z-index: 10000;
                min-width: 280px;
                transition: all 0.3s ease;
            `;
            document.body.appendChild(notification);
            this.activeNotifications.set(id, notification);
        }

        const progressPercent =
            total > 0 ? Math.round((current / total) * 100) : 0;
        notification.innerHTML = `
            <div>${message}</div>
            ${total > 0
                ? `
                <div style="margin-top: 8px;">
                    <div style="background: rgba(255,255,255,0.3); height: 4px; border-radius: 2px; overflow: hidden;">
                        <div style="background: #fff; height: 100%; width: ${progressPercent}%; transition: width 0.3s ease;"></div>
                    </div>
                    <div style="margin-top: 4px; font-size: 12px;">${current}/${total} (${progressPercent}%)</div>
                </div>
            `
                : ""
            }
        `;
    }

    hideProgress(id, finalMessage = null, type = "success") {
        const notification = this.activeNotifications.get(id);
        if (!notification) return;

        if (finalMessage) {
            const colors = {
                success: "#4CAF50",
                error: "#F44336",
                warning: "#FF9800",
            };

            notification.style.background = colors[type] || colors.success;
            notification.textContent = finalMessage;

            setTimeout(() => this.removeNotification(id), 3000);
        } else {
            this.removeNotification(id);
        }
    }

    removeNotification(id) {
        const notification = this.activeNotifications.get(id);
        if (notification) {
            notification.style.opacity = "0";
            notification.style.transform = "translateX(100%)";
            setTimeout(() => {
                notification.remove();
                this.activeNotifications.delete(id);
            }, 300);
        }
    }
}

const progressManager = new ProgressManager();

function setupOptimizedPasteHandler(editor) {
    let isProcessing = false; // Thêm biến cờ để kiểm soát
    editor.on("paste", async function (evt) {
        // Nếu đang xử lý thì bỏ qua
        if (isProcessing) return;
        isProcessing = true;

        const dataTransfer = evt.data.dataTransfer;
        let hasImageFiles = false;

        try {
            // Kiểm tra có file ảnh trong clipboard không
            if (dataTransfer && dataTransfer.getFilesCount() > 0) {
                for (let i = 0; i < dataTransfer.getFilesCount(); i++) {
                    const file = dataTransfer.getFile(i);
                    if (file.type.startsWith("image/")) {
                        hasImageFiles = true;
                        break;
                    }
                }
            }

            // Nếu có file ảnh thì xử lý và return luôn
            if (hasImageFiles) {
                evt.cancel();
                const files = [];
                for (let i = 0; i < dataTransfer.getFilesCount(); i++) {
                    const file = dataTransfer.getFile(i);
                    if (file.type.startsWith("image/")) {
                        files.push(file);
                    }
                }
                if (files.length > 0) {
                    await processClipboardFiles(editor, files);
                }
                return;
            }

            // Xử lý HTML paste
            const html = evt.data.dataValue;
            if (!html) return;

            const tempDiv = document.createElement("div");
            tempDiv.innerHTML = html;
            const images = tempDiv.querySelectorAll("img[src]");

            if (images.length === 0) return;

            evt.cancel();
            await processImagesPaste(editor, images, tempDiv);
        } finally {
            isProcessing = false; // Reset cờ khi xử lý xong
        }
    });
}

class OptimizedImageUploadManager {
    constructor(apiUrl, lightLogoUrl, darkLogoUrl) {
        this.apiUrl = apiUrl;
        this.logoManager = new OptimizedImageLogoManager(
            lightLogoUrl,
            darkLogoUrl
        );
        this.uploadQueue = [];
        this.isUploading = false;
    }

    async uploadImages(images) {
        const progressId = "image-upload";
        progressManager.showProgress(
            progressId,
            "Đang tải lên ảnh...",
            0,
            images.length
        );

        try {
            const batches = chunkArray(images, OPTIMIZATION_CONFIG.CHUNK_SIZE);
            let uploadedCount = 0;
            let results = [];

            for (const batch of batches) {
                const processedBatch = await this.processBatchWithLogo(batch);
                const batchResults = await this.uploadProcessedBatch(
                    processedBatch
                );
                results = [...results, ...batchResults];

                uploadedCount += batchResults.length; // Đếm cả ảnh gốc và ảnh có logo
                progressManager.showProgress(
                    progressId,
                    "Đang tải lên ảnh...",
                    uploadedCount,
                    images.length
                );

                if (batches.indexOf(batch) < batches.length - 1) {
                    await new Promise((resolve) =>
                        setTimeout(
                            resolve,
                            OPTIMIZATION_CONFIG.PROCESSING_DELAY
                        )
                    );
                }
            }

            progressManager.hideProgress(
                progressId,
                `Đã tải lên ${uploadedCount}/${images.length} ảnh thành công!`
            );
            return results;
        } catch (error) {
            progressManager.hideProgress(
                progressId,
                "Có lỗi khi tải lên ảnh",
                "error"
            );
            throw error;
        }
    }

    async processBatchWithLogo(batch) {
        const processedItems = [];

        for (const [index, image] of batch.entries()) {
            try {
                let imageUrl;

                // Xử lý tùy theo loại dữ liệu đầu vào
                if (image.file) {
                    imageUrl = await new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onload = (e) => resolve(e.target.result);
                        reader.readAsDataURL(image.file);
                    });
                } else if (image.base64) {
                    imageUrl = image.base64;
                } else if (image.url) {
                    imageUrl = image.url;
                } else {
                    throw new Error("Không có dữ liệu ảnh hợp lệ");
                }

                // Lưu ảnh gốc
                const originalImage = imageUrl;

                // Thêm logo vào ảnh
                const processedImage =
                    await this.logoManager.processSingleImage(imageUrl);

                processedItems.push({
                    ...image,
                    originalData: originalImage, // Dữ liệu ảnh gốc (base64)
                    processedData: processedImage.dataUrl, // Dữ liệu ảnh có logo (base64)
                    originalWidth: processedImage.originalWidth,
                    originalHeight: processedImage.originalHeight,
                    filename: `bai_viet_${Date.now()}_${index}.jpg`, // Tên file chung cho cả hai
                });
            } catch (error) {
                console.warn("Lỗi khi xử lý ảnh:", error);
                processedItems.push({
                    ...image,
                    error: error.message,
                });
            }
        }

        return processedItems;
    }

    // Sửa lại hàm uploadProcessedBatch trong OptimizedImageUploadManager
    async uploadProcessedBatch(processedBatch) {
        const results = [];
        const validItems = processedBatch.filter(
            (item) => item.processedData && item.originalData && !item.error
        );

        try {
            // XỬ LÝ TỪNG ẢNH RIÊNG BIỆT ĐỂ ĐẢM BẢO CÙNG TÊN FILE
            for (const item of validItems) {
                const formData = new FormData();

                // Tạo tên file chung cho cả 2 ảnh
                const timestamp = Date.now();
                const randomId = Math.floor(Math.random() * 9999);
                const commonFilename = `bai_viet_${timestamp}_${randomId}.jpg`;

                // Tạo blob từ base64 cho ảnh gốc
                const originalBlob = await (
                    await fetch(item.originalData)
                ).blob();
                const originalFile = new File([originalBlob], commonFilename, {
                    type: "image/jpeg",
                });

                // Tạo blob từ base64 cho ảnh có logo
                const processedBlob = await (
                    await fetch(item.processedData)
                ).blob();
                const processedFile = new File(
                    [processedBlob],
                    commonFilename,
                    {
                        type: "image/jpeg",
                    }
                );

                // Thêm vào FormData theo thứ tự: gốc trước, logo sau
                formData.append("files[]", originalFile);
                formData.append("files[]", processedFile);

                // Thêm folders tương ứng
                formData.append("folders[]", "bai-viet/anh-goc");
                formData.append("folders[]", "bai-viet/noi-dung");

                // Gửi request upload
                const response = await fetch(this.apiUrl, {
                    method: "POST",
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                // Kiểm tra kết quả upload
                if (data.success && data.success.length >= 1) {
                    // data.success[0] = ảnh gốc
                    // data.success[1] = ảnh có logo
                    results.push({
                        originalUrl: data.success[0].url,
                        logoUrl: data.success[0].url,
                        element: item.element,
                        originalWidth: item.originalWidth,
                        originalHeight: item.originalHeight,
                        filename: commonFilename, // Thêm tên file để debug
                    });

                    console.log(results);
                    
                } else {
                    results.push({
                        error: data.error || "Upload failed",
                        element: item.element,
                    });
                }
            }

            return results;
        } catch (error) {
            console.error("Upload batch error:", error);
            throw error;
        }
    }
}
const uploadManager = new OptimizedImageUploadManager(
    APIMEDIAURL,
    LIGHT_LOGO_URL,
    DARK_LOGO_URL
);

async function processClipboardFiles(editor, files) {
    const progressId = "clipboard-files";
    progressManager.showProgress(
        progressId,
        "Đang xử lý ảnh từ clipboard...",
        0,
        files.length
    );

    try {
        const uploadItems = [];

        for (const file of files) {
            const reader = new FileReader();
            const base64 = await new Promise((resolve) => {
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });

            uploadItems.push({
                base64: base64,
                file: file,
            });
        }

        // Upload ảnh lên server
        const uploadResults = await uploadManager.uploadImages(uploadItems);

        // DEBUG: Kiểm tra kết quả upload
        // debugUploadResults(uploadResults);

        // Chèn ảnh có logo vào editor
        const successfulUploads = uploadResults.filter((r) => r.logoUrl);
        let insertedCount = 0;

        // TẠO HTML CHO TẤT CẢ ẢNH TRƯỚC KHI CHÈN
        let allImagesHtml = "";

        for (const result of successfulUploads) {
            const imgHtml = `<img 
                src="${result.logoUrl}" 
                style="max-width:100%;height:auto;display:block;margin:10px 0;" 
                data-has-logo="true"
                data-original-src="${result.originalUrl}"
                data-original-width="${result.originalWidth}"
                data-original-height="${result.originalHeight}"
                data-filename="${result.filename}"
            >`;

            // Thêm vào HTML tổng thay vì chèn từng ảnh
            allImagesHtml += imgHtml;
            insertedCount++;

            progressManager.showProgress(
                progressId,
                "Đang chuẩn bị chèn ảnh...",
                insertedCount,
                successfulUploads.length
            );
        }

        // CHÈN TẤT CẢ ẢNH MỘT LẦN DUY NHẤT
        if (allImagesHtml) {
            editor.insertHtml(allImagesHtml);
        }

        progressManager.hideProgress(
            progressId,
            `Đã tải lên và chèn ${insertedCount} ảnh thành công!`
        );
    } catch (error) {
        console.error("Lỗi xử lý clipboard files:", error);
        progressManager.hideProgress(
            progressId,
            "Có lỗi khi xử lý ảnh",
            "error"
        );
    }
}

async function processImagesPaste(editor, images, container) {
    const progressId = "paste-images";
    progressManager.showProgress(
        progressId,
        `Đang xử lý ${images.length} ảnh...`,
        0,
        images.length
    );

    try {
        // Chuẩn bị dữ liệu upload
        const uploadItems = [];

        for (const img of images) {
            uploadItems.push({
                url: img.src,
                element: img,
                originalWidth: img.naturalWidth,
                originalHeight: img.naturalHeight,
            });
        }

        // Upload ảnh lên server
        const uploadResults = await uploadManager.uploadImages(uploadItems);

        // DEBUG: Kiểm tra kết quả upload
        // debugUploadResults(uploadResults);

        // Cập nhật ảnh trong container
        let processedCount = 0;

        for (const result of uploadResults) {
            if (result.logoUrl && result.element) {
                result.element.src = result.logoUrl;
                result.element.setAttribute("data-has-logo", "true");
                result.element.setAttribute(
                    "data-original-src",
                    result.originalUrl
                );
                result.element.setAttribute("data-filename", result.filename);
                result.element.style.maxWidth = "100%";
                result.element.style.height = "auto";
            }

            processedCount++;
            progressManager.showProgress(
                progressId,
                "Đang cập nhật ảnh...",
                processedCount,
                uploadResults.length
            );
        }

        // Chèn HTML đã xử lý vào editor
        editor.insertHtml(container.innerHTML);
        progressManager.hideProgress(
            progressId,
            `Đã xử lý ${processedCount} ảnh thành công!`
        );
    } catch (error) {
        console.error("Lỗi xử lý ảnh:", error);
        progressManager.hideProgress(
            progressId,
            "Có lỗi khi xử lý ảnh",
            "error"
        );

        // Fallback: chèn HTML gốc
        editor.insertHtml(container.innerHTML);
    }
}

function chunkArray(array, chunkSize) {
    const chunks = [];
    for (let i = 0; i < array.length; i += chunkSize) {
        chunks.push(array.slice(i, i + chunkSize));
    }
    return chunks;
}

function setupImageResizeObserver(editor) {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (
                mutation.type === "attributes" &&
                mutation.attributeName === "style"
            ) {
                const img = mutation.target;
                if (
                    img.tagName === "IMG" &&
                    img.hasAttribute("data-has-logo")
                ) {
                    // Debounce để tránh xử lý quá nhiều
                    clearTimeout(img._resizeTimeout);
                    img._resizeTimeout = setTimeout(
                        () => updateImageLogo(img),
                        200
                    );
                }
            }
        });
    });

    const editorDocument = editor.document.$;
    observer.observe(editorDocument.body, {
        attributes: true,
        attributeFilter: ["style"],
        subtree: true,
    });

    return observer;
}

async function updateImageLogo(imgElement) {
    try {
        const originalSrc = imgElement.getAttribute("data-original-src");
        if (!originalSrc) return;

        const currentWidth = imgElement.offsetWidth || imgElement.width;
        const lastWidth = parseInt(
            imgElement.getAttribute("data-last-width") || "0"
        );

        if (Math.abs(currentWidth - lastWidth) < 10) return;

        const result = await optimizedLogoManager.processSingleImage(
            originalSrc
        );
        imgElement.src = result.dataUrl;
        imgElement.setAttribute("data-last-width", currentWidth.toString());
    } catch (error) {
        console.warn("Lỗi khi cập nhật logo:", error);
    }
}

CKEDITOR.on("instanceReady", function (ev) {
    const editor = ev.editor;
    setupOptimizedPasteHandler(editor);
    setupImageResizeObserver(editor);

    editor.document.on("click", function (evt) {
        const target = evt.data.getTarget();
        if (target.getName() === "img") {
            let parent = target.getParent();
            for (let i = 0; i < 2 && parent; i++) {
                if (parent.getName && parent.getName() === "figure") {
                    return; // đã có <figure>, không cần xử lý
                }
                parent = parent.getParent();
            }
            if (!parent) {
                return;
            }

            editor.execCommand("image");
            editor.once("dialogShow", function (evt) {
                const dialog = evt.data;
                setTimeout(() => {
                    const checkbox = document.querySelector(
                        ".cke_dialog_ui_checkbox_input"
                    );
                    if (checkbox) {
                        checkbox.checked = true;
                        checkbox.dispatchEvent(
                            new Event("change", { bubbles: true })
                        );
                    }
                    const okButton = document.querySelector(
                        ".cke_dialog_ui_button_ok"
                    );
                    if (okButton) {
                        okButton.click();
                        let pr = target.getParent().getParent();
                        if (pr.getName && pr.getName() === "figure") {
                            let figcaption = pr.findOne("figcaption");
                            if (figcaption) {
                                let range = editor.createRange();
                                range.moveToPosition(
                                    figcaption,
                                    CKEDITOR.POSITION_BEFORE_END
                                );
                                editor.getSelection().selectRanges([range]);
                                editor.focus();
                            }
                        }
                    }
                }, 0);
            });
        }
    });
});

const optimizedStyle = document.createElement("style");
optimizedStyle.textContent = `
    .cke_editable img[data-has-logo] {
        transition: all 0.2s ease;
        cursor: move;
    }
    
    .cke_editable img[data-has-logo]:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .cke_editable img[data-compressed="true"] {
        border: 2px solid #4CAF50;
        border-radius: 4px;
    }
    
    .cke_editable img[data-compressed="true"]::after {
        content: "✓ Optimized";
        position: absolute;
        top: 5px;
        left: 5px;
        background: #4CAF50;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: bold;
    }

    .cke_editable figure.image {
        border: none !important;
        background: none !important;
        padding: 0 !important;
        margin: 0 auto 10px !important;
        max-width: 100% !important;
    }

    .cke_editable figure.image img {
        max-width: 100% !important;
        height: auto !important;
        display: block;
        margin: 0 auto;
    }

    .cke_editable figure.image figcaption {
        font-size: 14px;
        color: #666;
        font-style: italic;
        text-align: center;
        margin-top: 4px;
    }

    * Fix ảnh từ Google Docs */
    .cke_editable img,
    .cke_editable img {
        display: block !important;
        margin: 10px 0 !important;
        max-width: 100% !important;
        height: auto !important;
        clear: both !important;
    }
    
    .cke_editable img + img {
        margin-top: 15px !important;
    }
    
    .cke_editable table img {
        display: block;
        margin: 0 auto;
    }
`;
document.head.appendChild(optimizedStyle);

function debugUploadResults(results) {
    console.log("=== UPLOAD RESULTS DEBUG ===");
    results.forEach((result, index) => {
        if (result.originalUrl && result.logoUrl) {
            console.log(`Image ${index + 1}:`);
            console.log(`  Original: ${result.originalUrl}`);
            console.log(`  Logo: ${result.logoUrl}`);
            console.log(`  Filename: ${result.filename}`);

            const originalFilename = result.originalUrl.split("/").pop();
            const logoFilename = result.logoUrl.split("/").pop();

            if (originalFilename === logoFilename) {
                console.log(`  ✅ Tên file khớp: ${originalFilename}`);
            } else {
                console.log(`  ❌ Tên file KHÔNG khớp:`);
                console.log(`    Original: ${originalFilename}`);
                console.log(`    Logo: ${logoFilename}`);
            }
        }
    });
    console.log("=== END DEBUG ===");
}
