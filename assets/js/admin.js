var went_wrong = comparrot.went_wrong;
(function ($) {
    $(document).ready(() => {
        // File uploader ajax

        regenerateTemplates();
        // Toggle button
        $(`.cp-toggle`).on(`click`, function (e) {
            let self = $(this);

            let current = self.find(`.active-toggle`);
            let toggles = self.find(`.toggle-item`);

            toggles.each(function (e) {
                if (!$(this).hasClass(`active-toggle`)) {
                    $(this).addClass(`active-toggle`);
                } else {
                    $(this).removeClass(`active-toggle`);
                }
            });

            self.data(
                `value`,
                self.find(`.toggle-item.active-toggle`).data(`value`)
            );

            $.ajax({
                type: "post",
                url: comparrot.ajax_url,
                data: {
                    action: `save_toggle_value`,
                    key: self.data(`key`),
                    value: self.data(`value`),
                    nonce: comparrot.save_toggle_value_nonce,
                },
                dataType: "json",
                success: function (res) {},
            });
        });

        // // Development page regeneration
        // $(`.re-generate-page-btn`).on(`click`, function (e) {
        //     let data = {
        //         action: `regenerate_pages`,
        //         nonce: comparrot.regenerate_pages_nonce,
        //     };

        //     let self = $(this);

        //     self.toggleClass(`rotating`);
        //     $.ajax({
        //         type: "POST",
        //         url: comparrot.ajax_url,
        //         data,
        //         dataType: "JSON",
        //         success: function (response) {
        //             console.log(response)
        //             if (response.success) {
        //                 // success(response.data.msg);
        //             } else {
        //                 // failed(response.data.msg);
        //             }
        //         },
        //         complete: (response) => {
       
        //             self.toggleClass(`rotating`);
        //         },
        //         error: function(response){
        //             alert(123)
        //         }
        //     });
        // });

        show_lightbox();
        logo_selector();
    });

    // Save settings
    $(`.comparrot-settings-save-button`).on(`click`, save_settings);
    template_download();
    resetTheme();
})(jQuery);

/**
 * Converts object to form data
 * @param {object} obj
 */
function obj2frm(obj) {
    let data = new FormData();
    for (let key in data) {
        data.append(key, data);
    }
    return data;
}

/**
 * Animates loading animation
 * @param {object} elem
 * @param {bool} isend
 */
function load_animation(elem, isend = false) {
    let $ = jQuery;
    if (!isend) {
        $(elem).addClass(`is-loading`);
    } else {
        $(elem).removeClass(`is-loading`);
    }
}

/**
 * Shows success message
 *
 * @param {mixed} status
 */
function success(status) {
    jQuery(`.comparrot-success`).parent().remove();
    jQuery(`.cp-admin-body`).prepend(
        `<div><div class="comparrot-success">${status}</div></div>`
    );
    jQuery(`.comparrot-success:last-child`).hide(0, function (e) {
        jQuery(this).show(500);
    });
    let last = jQuery(`.comparrot-success:last-child`);

    setTimeout(() => {
        last.hide(300, function (e) {
            jQuery(this).parent().remove();
        });
    }, 2500);
}

/**
 * Shows failed message
 *
 * @param {mixed} status
 */
function failed(status) {
    jQuery(`.cp-admin-body`).prepend(
        `<div class="comparrot-failed">${status}</div>`
    );

    jQuery(`.comparrot-failed`).hide(0, function (e) {
        jQuery(this).show(500);
    });
}

function show_lightbox() {
    let $ = jQuery;
    $(`.help-btn`).click(function (e) {
        $(`.cp-admin-help-shadow`)
            .removeClass(`lightbox-hide`)
            .addClass(`lightbox-show`);
    });

    $(`.cp-admin-help .close-button`).click(function (e) {
        $(`.cp-admin-help-shadow`)
            .removeClass(`lightbox-show`)
            .addClass(`lightbox-hide`);
    });
}

function save_settings() {
    let $ = jQuery;

    $(`.comparrot-ajax-status`).html(
        `<div class="spinner settings-spinner"></div>  Saving the settings....`
    );
    $(`.settings-spinner`).addClass(`is-active`);

    $(`.comparrot-settings-form`).each(function (index, element) {
        let data = $(element).serialize();
        $.ajax({
            type: "POST",
            url: comparrot.ajax_url,
            data: data + `&action=comparrot_save_settings`,
            dataType: "json",
            success: function (res) {
                console.log(res);
                $(`.settings-spinner`).removeClass(`is-active`);
                if (res.success) {
                    success(res.data.message);
                }
            },
            error: function (res) {
                $(`.settings-spinner`).removeClass(`is-active`);
                alert(went_wrong);
            },
            complete: function (res) {
                $(`.comparrot-ajax-status`).html(``);
            },
        });
    });
}

/**
 * Downloads the CSV template
 */
function template_download() {
    let $ = jQuery;

    $(`.template-download`).on(`click`, function (e) {
        $.ajax({
            type: "POST",
            url: comparrot.ajax_url,
            data: {
                action: `download_csv_template`,
                nonce: comparrot.download_csv_template_nonce,
            },
            dataType: "JSON",
            success: function (res) {
                if (res.success) {
                    download_file(res.data.url);
                } else {
                    alert(res.data.msg);
                }
            },
            error: function (res) {
                alert(went_wrong);
            },
        });
    });
}

/**
 * Force download a file instead of open in browser
 *
 * @param {string} file_path
 */
function download_file(file_path) {
    var a = document.createElement("a");
    a.href = file_path;
    a.download = file_path.substr(file_path.lastIndexOf("/") + 1);
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

/**
 * Wordpress media uploader to select the logo
 */
function logo_selector() {
    let $ = jQuery;

    $(`.logo-selector`).on(`click`, function (e) {
        e.preventDefault();

        let self = $(this);

        let media_uploader = (wp.media.frames.file_frame = wp.media({
            title: `Select website logo for Comparrot theme`,
            library: {
                type: `image`,
            },
            button: {
                text: `Select logo`,
            },
            multiple: false,
        }));

        media_uploader.on(`select`, function () {
            let attachment = media_uploader
                .state()
                .get(`selection`)
                .first()
                .toJSON();

            $.ajax({
                type: "post",
                url: comparrot.ajax_url,
                data: {
                    action: `save_logo_url`,
                    url: attachment.url,
                },
                dataType: "json",
                success: function (response) {
                    if (response.success) {
                        $(`.logo-selector-holder img`).attr(
                            `src`,
                            attachment.url
                        );
                    }
                },
                error: function (res) {
                    alert(went_wrong);
                },
            });
        });

        media_uploader.open();
    });
}

function resetTheme() {
    let $ = jQuery;
    let btn = $(`.comparrot-settings-reset-button`);

    btn.on(`click`, function (e) {
        if (
            confirm(
                `!!!!Alert!!!!\nClicking on 'ok' will rest all your theme settings.\nAre you sure to reset?`
            )
        ) {
            $.ajax({
                type: "POST",
                url: comparrot.ajax_url,
                data: {
                    action: `compt_reset_theme`,
                    nonce: comparrot.compt_reset_theme_nonce,
                },
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.data.msg);
                    }
                },
                error: function (res) {
                    alert(went_wrong);
                },
                complete: function (res) {},
            });
        }
    });
}

// New functions
(function ($) {
    $(document).ready(() => {
        saveSettingsForm();
        manageUplaod();
    });
})(jQuery);

/**
 * Saves Schema generated settings form by AJAX request
 */
function saveSettingsForm() {
    let $ = jQuery;
    let forms = $(`.comparrot-settings-form`);

    $(`.comparrot-save-settings`).on(`click`, function (e) {
        let button = $(this);
        button.addClass(`is-loading`);
        forms.each(function (i, element) {
            let data = $(this).serialize();
            let form_name = $(this).data(`name`);
            data += `&action=comparrot_save_settings&nonce=${comparrot.comparrot_save_settings_nonce}&form=${form_name}`;

            $.ajax({
                type: "POST",
                url: comparrot.ajax_url,
                data: data,
                dataType: "JSON",
                success: function (res) {
                    console.log(res);
                    if (res.success) {
                        success(res.data.msg);
                    }
                },
                error: function (res) {
                    error(went_wrong);
                },
                complete: function (res) {
                    button.removeClass(`is-loading`);
                },
            });
        });
    });
}

function manageUplaod() {
    let $ = jQuery;
    $(`.uploader .upload-btn`).on(`click`, function (e) {
        let elem = document.createElement(`input`);
        elem.type = `file`;
        elem.name = `csv_file`;
        elem.id = `csv_file`;
        elem.multiple = `multiple`;

        $(elem)
            .trigger(`click`)
            .on(`change`, function (e) {
                load_animation(`.uploader .loader`);

                let data = new FormData();
                data.append("action", "comparrot_upload_file");
                data.append(`nonce`, comparrot.comparrot_upload_file_nonce);
                data.append(`default_type`, $(`#default_type`).data(`value`));
                data.append(
                    `default_status`,
                    $(`#default_status`).data(`value`)
                );
                data.append(
                    `default_layout`,
                    $(`#default_layout`).data(`value`)
                );
                data.append(`delimiter`, $(`#csv_delimiter`).data(`value`));

                $.each($(this)[0].files, function (i, file) {
                    data.append("all_files[]", file);
                });

                $.ajax({
                    type: "POST",
                    url: comparrot.ajax_url,
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        // console.log(response);
                        // return;
                        // return;
                        let res = JSON.parse(response);

                        if (res.success == false) {
                            failed(res.msg);
                            return;
                        }
                        if (res.success == true) {
                            success(res.msg);
                            return;
                        }

                        success(`Successfully imported pages using CSV.`);
                        $(`.upload-log`).html(
                            `<h2>Inserted</h2>${res.inserted}`
                        );
                        $(`.skipped-log`).html(
                            `<h2>Skipped</h2>${res.skipped}`
                        );
                    },
                    error: (res) => {
                        failed(`Failed to load pages using CSV.`);
                    },
                    complete: (res) => {
                        load_animation(`.uploader .loader`, true);
                    },
                });
            });
    });
}

function regenerateTemplates() {
    let $ = jQuery;

    $(`.re-generate-btn`).on(`click`, function (e) {
        e.preventDefault();
        let self = $(this);
        self.toggleClass(`rotating`);
        $.ajax({
            type: "POST",
            url: comparrot.ajax_url,
            data: {
                action: `regenerate_templates`,
                nonce: comparrot.regenerate_templates_nonce,
            },
            dataType: "JSON",
            success: function (response) {
                if (response.success) {
                    success(response.data.msg);
                } else {
                    failed(response.data.msg);
                }
            },
            complete: function (res) {
                self.toggleClass(`rotating`);
            },
        });
    });
}
