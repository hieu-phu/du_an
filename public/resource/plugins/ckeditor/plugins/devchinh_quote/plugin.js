// ===== PLUGIN CẬP NHẬT HỖ TRỢ AVATAR IMAGE =====
CKEDITOR.plugins.add("devchinh_quote", {
    requires: "widget",
    icons: "left",
    lang: ["en", "vi"],
    init: function (editor) {
        // Cấu hình allowedContent bao gồm img
        editor.config.extraAllowedContent = 
            'div(devchinh__cards-container,devchinh__card--neon,devchinh__quote-icon,devchinh__author,devchinh__author-info,devchinh__author-avatar-container); ' +
            'img(devchinh__author-avatar-img)[src,style]; ' +
            'div(devchinh__author-avatar-text); ' +
            'i(devchinh__icon,fas,fa-quote-right); ' +
            'p(devchinh__quote); ' +
            'span(devchinh__author-name,devchinh__author-title); ' +
            'div(devchinh__author-avatar)';

        // đăng ký dialog file
        CKEDITOR.dialog.add(
            "devchinh_quoteDialog",
            this.path + "dialogs/devchinh_quote.js"
        );

        // command để mở dialog
        editor.addCommand(
            "devchinh_quoteDialog",
            new CKEDITOR.dialogCommand("devchinh_quoteDialog")
        );

        // nút toolbar
        editor.ui.addButton("devchinh_quote", {
            label: (editor.lang.devchinh_quote && editor.lang.devchinh_quote.button) || "Chèn trích dẫn",
            command: "devchinh_quoteDialog",
            toolbar: "insert",
            icon: this.path + "icons/left.png",
        });

        // widget definition
        editor.widgets.add("devchinh_quote", {
             allowedContent: true,
            dialog: "devchinh_quoteDialog",
            
            allowedContent: {
                'div': {
                    classes: 'devchinh__cards-container,devchinh__card--neon,devchinh__quote-icon,devchinh__author,devchinh__author-info,devchinh__author-avatar,devchinh__author-avatar-container,devchinh__author-avatar-text'
                },
                'img': {
                    classes: 'devchinh__author-avatar-img',
                    attributes: ['src', 'style']
                },
                'i': {
                    classes: 'devchinh__icon,fas,fa-quote-right'
                },
                'p': {
                    classes: 'devchinh__quote'
                },
                'span': {
                    classes: 'devchinh__author-name,devchinh__author-title'
                }
            },
            
            requiredContent: 'div(devchinh__cards-container)',
            
            // Template cơ bản với text avatar
            template:
                '<div class="devchinh__cards-container">' +
                '  <div class="devchinh__card--neon devchinh__quote-icon">' +
                '    <i class="devchinh__icon fas fa-quote-right"></i>' +
                '    <p class="devchinh__quote">"Nội dung mẫu"</p>' +
                '    <div class="devchinh__author">' +
                '      <div class="devchinh__author-avatar-container">' +
                '        <div class="devchinh__author-avatar-text">AV</div>' +
                '      </div>' +
                '      <div class="devchinh__author-info">' +
                '        <span class="devchinh__author-name">Tên tác giả</span>' +
                '        <span class="devchinh__author-title">Tác giả</span>' +
                "      </div>" +
                "    </div>" +
                "  </div>" +
                "</div>",

            upcast: function (element) {
                return element.name === "div" && element.hasClass("devchinh__cards-container");
            },

            init: function () {
                var quoteEl = this.element.findOne(".devchinh__quote");
                var nameEl = this.element.findOne(".devchinh__author-name");
                
                // Tìm avatar (có thể là img hoặc div)
                var avatarImgEl = this.element.findOne(".devchinh__author-avatar-img");
                var avatarTextEl = this.element.findOne(".devchinh__author-avatar-text");
                
                this.setData("quote", quoteEl ? quoteEl.getText().replace(/^"|"$/g, "") : "");
                this.setData("author", nameEl ? nameEl.getText() : "");
                
                if (avatarImgEl) {
                    this.setData("avatarType", "image");
                    this.setData("avatarSrc", avatarImgEl.getAttribute("src"));
                } else if (avatarTextEl) {
                    this.setData("avatarType", "text");
                    this.setData("avatarText", avatarTextEl.getText());
                }
            },

            data: function () {
                if (this.data.quote !== undefined) {
                    var q = this.element.findOne(".devchinh__quote");
                    if (q) q.setText('"' + this.data.quote + '"');
                }
                if (this.data.author !== undefined) {
                    var n = this.element.findOne(".devchinh__author-name");
                    if (n) n.setText(this.data.author);
                }
                
                // Cập nhật avatar
                if (this.data.avatarType && this.data.avatarType !== undefined) {
                    var container = this.element.findOne(".devchinh__author-avatar-container");
                    if (container) {
                        if (this.data.avatarType === "image" && this.data.avatarSrc) {
                            container.setHtml('<img src="' + this.data.avatarSrc + '" class="devchinh__author-avatar-img" style="width:60px; height:60px; border-radius:50%; object-fit:cover; border:3px solid white; box-shadow:0 4px 6px rgba(0,0,0,0.1);">');
                        } else if (this.data.avatarType === "text" && this.data.avatarText) {
                            container.setHtml('<div class="devchinh__author-avatar-text">' + CKEDITOR.tools.htmlEncode(this.data.avatarText) + '</div>');
                        }
                    }
                }
            },

            downcast: function (element) {
                // Đảm bảo cấu trúc đúng khi downcast
                return element;
            }
        });
    },
});