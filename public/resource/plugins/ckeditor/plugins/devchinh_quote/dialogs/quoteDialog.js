CKEDITOR.dialog.add("devchinh_quoteDialog", function (editor) {
    return {
        title: editor.lang.devchinh_quote.dialogTitle || "Chèn trích dẫn",
        minWidth: 400,
        minHeight: 200,
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
                        setup: function () {},
                        commit: function (widget) {},
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
                        type: "text",
                        id: "avatar",
                        label: "Ký tự avatar (VD: GM, NL...)",
                    },
                ],
            },
        ],
        onOk: function () {
            var dialog = this;
            var quote = dialog.getValueOf("info", "quote");
            var author = dialog.getValueOf("info", "author");
            var avatar =
                dialog.getValueOf("info", "avatar") ||
                author.substring(0, 2).toUpperCase();

            var html =
                '<div class="panorama-card">' +
                '  <div class="panorama-info">' +
                '    <div class="author">' +
                '      <div class="author-avatar">' +
                CKEDITOR.tools.htmlEncode(avatar) +
                "</div>" +
                '      <div class="author-name">' +
                CKEDITOR.tools.htmlEncode(author) +
                "</div>" +
                "    </div>" +
                '    <div class="panorama-title">' +
                CKEDITOR.tools.htmlEncode(quote) +
                "</div>" +
                "  </div>" +
                "</div>";

            editor.insertHtml(html);
        },
    };
});
