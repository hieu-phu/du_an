CKEDITOR.dialog.add("devchinh_quoteDialog", function (editor) {
    return {
        title: editor.lang.devchinh_quote.dialogTitle || "Chèn trích dẫn",
        minWidth: 450,
        minHeight: 400,
        contents: [
            {
                id: "info",
                label: "Thông tin",
                elements: [
                    {
                        type: "text",
                        id: "quote",
                        label: "Nội dung trích dẫn",
                        validate: CKEDITOR.dialog.validate.notEmpty(
                            "Vui lòng nhập nội dung."
                        ),
                    },
                    {
                        type: "text",
                        id: "author",
                        label: "Tên tác giả",
                        validate: CKEDITOR.dialog.validate.notEmpty(
                            "Vui lòng nhập tác giả."
                        ),
                    },
                    {
                        type: "html",
                        html: '<div style="margin: 10px 0;">' +
                              '<label style="display:block; margin-bottom:5px; font-weight:bold;">Tải lên ảnh avatar:</label>' +
                              '<input type="file" id="avatarFileInput" accept="image/*" style="margin-bottom:10px;">' +
                              '<button type="button" id="clearAvatarBtn" style="margin-left:10px; padding:5px 10px;">Xóa ảnh</button>' +
                              '<div id="avatarPreview" style="margin-top:10px; text-align:center; display:none;"></div>' +
                              '</div>'
                    },
                    {
                        type: "text",
                        id: "avatarText",
                        label: "Hoặc nhập ký tự avatar (VD: GM, NL...)",
                        'default': ''
                    },
                    {
                        type: "text",
                        id: "avatarData",
                        label: "",
                        style: "display:none;",
                        'default': ''
                    }
                ],
            },
        ],
        
        onShow: function() {
            var dialog = this;
            
            // Xử lý upload file
            setTimeout(function() {
                var fileInput = document.getElementById('avatarFileInput');
                var clearBtn = document.getElementById('clearAvatarBtn');
                var preview = document.getElementById('avatarPreview');
                
                if (fileInput) {
                    fileInput.onchange = function(e) {
                        var file = e.target.files[0];
                        
                        if (file) {
                            // Kiểm tra loại file
                            if (!file.type.match(/^image\/(jpeg|jpg|png|gif|webp)$/)) {
                                alert('Vui lòng chọn file ảnh (JPG, PNG, GIF, WebP)');
                                fileInput.value = '';
                                return;
                            }
                            
                            // Kiểm tra kích thước file (max 5MB)
                            if (file.size > 5 * 1024 * 1024) {
                                alert('Kích thước file không được vượt quá 5MB');
                                fileInput.value = '';
                                return;
                            }
                            
                            // Đọc file và chuyển thành base64
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                // Lưu base64
                                dialog.setValueOf('info', 'avatarData', e.target.result);
                                
                                // Hiển thị preview
                                preview.innerHTML = '<img src="' + e.target.result + '" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #00ffff; box-shadow:0 0 10px rgba(0,255,255,0.3);">';
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        }
                    };
                }
                
                if (clearBtn) {
                    clearBtn.onclick = function() {
                        if (fileInput) fileInput.value = '';
                        dialog.setValueOf('info', 'avatarData', '');
                        if (preview) {
                            preview.style.display = 'none';
                            preview.innerHTML = '';
                        }
                    };
                }
            }, 100);
        },

        onOk: function () {
            var dialog = this;
            var quote = dialog.getValueOf("info", "quote");
            var author = dialog.getValueOf("info", "author");
            var avatarData = dialog.getValueOf("info", "avatarData");
            var avatarText = dialog.getValueOf("info", "avatarText");
            
            var avatarHtml = '';
            
            if (avatarData) {
                // Sử dụng ảnh được upload
                avatarHtml = '<div class="devchinh__author-avatar-container">' +
                           '<img src="' + avatarData + '" class="devchinh__author-avatar-img" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:3px solid white; box-shadow:0 4px 6px rgba(0,0,0,0.1);">' +
                           '</div>';
            } else {
                // Sử dụng text avatar
                var avatarTextFinal = avatarText || author.substring(0, 2).toUpperCase();
                avatarHtml = '<div class="devchinh__author-avatar-container">' +
                           '<div class="devchinh__author-avatar-text" style="width:60px; height:60px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; font-weight:bold; color:#ffffff; font-size:16px; border:3px solid white; box-shadow:0 4px 6px rgba(0,0,0,0.1);">' + 
                           CKEDITOR.tools.htmlEncode(avatarTextFinal) + 
                           '</div>' +
                           '</div>';
            }

            var element = CKEDITOR.dom.element.createFromHtml(
                '<div class="devchinh__cards-container">' +
                    '<div class="devchinh__card--neon devchinh__quote-icon">' +
                    '  <i class="devchinh__icon fas fa-quote-right"></i>' +
                    '  <p class="devchinh__quote">"' +
                    CKEDITOR.tools.htmlEncode(quote) +
                    '"</p>' +
                    '  <div class="devchinh__author">' +
                    avatarHtml +
                    '    <div class="devchinh__author-info">' +
                    '      <span class="devchinh__author-name">' +
                    CKEDITOR.tools.htmlEncode(author) +
                    "</span>" +
                    '      <span class="devchinh__author-title">Tác giả</span>' +
                    "    </div>" +
                    "  </div>" +
                    "</div>" +
                    "</div>"
            );

            // Thêm widget vào editor
            editor.insertElement(element);
            
            // Khởi tạo widget sau khi insert
            setTimeout(function() {
                var widgets = editor.widgets.instances;
                for (var id in widgets) {
                    if (widgets[id].name === 'devchinh_quote' && !widgets[id].ready) {
                        widgets[id].ready = true;
                        break;
                    }
                }
            }, 100);
        }
    }

});