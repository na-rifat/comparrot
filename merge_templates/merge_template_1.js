(function ($) {
    // Menu functions
    handleMenu();
    toggle_accordion();
    comptToc();

    $(`.cta-full-width-container`).mousemove(function (e) {
        var x = e.pageX;
        var y = e.pageY;
        x /= 70;
        y /= 70;
        // x = x % 20;
        // y = y % 20;

        $(".compt-cta-background-image").css({
            // "background-position": `${x}px ${y}px`,
            transform: `translate(${x}px, ${y}px)`,
        });
    });
})(jQuery);
/**
 * Handles menu functions
 */
function handleMenu() {
    let $ = jQuery;
    $(`.nav-toggler`).on(`click`, function (e) {
        if ($(`.comparrot_primary_menu_container `).css("display") == `none`) {
            $(`.comparrot_primary_menu_container `).slideDown(
                300,
                function (e) {}
            );
        } else {
            $(`.comparrot_primary_menu_container `).slideUp(300, function (e) {
                // $(this).css({
                //     display: `none`,
                // });
            });
        }
    });
    // $(`.comparrot_primary_menu_container `).toggle();
}

function toggle_accordion() {
    let $ = jQuery;

    $(`.compt-accordion-key`).on(`click`, function (e) {
        let content = $(`.compt-accordion-content`);
        let ico = $(`.compt-accordion .tog-btn`);
        if (content.css(`display`) == `none`) {
            content.slideDown(300);
            ico.css({ transform: `rotate(180deg)` });
        } else {
            content.slideUp(300);
            ico.css({ transform: `rotate(0deg)` });
        }
    });
}

function comptToc() {
    let $ = jQuery;
    if ($(`.toc-section`).length == 0) {
        return;
    }

    let ids = [];

    $(
        `.page-content-section h1, 
        .page-content-section h2, 
        .page-content-section h3,
        .page-content-section h4`
    ).each(function () {
        let id = createID($(this), ids);
        ids.push(id);
        $(this).attr(`id`, id);
    });

    tocbot.init({
        // Where to render the table of contents.
        tocSelector: ".compt-accordion-content",
        // Where to grab the headings to build the table of contents.
        contentSelector: ".page-content-section",
        // Which headings to grab inside of the contentSelector element.
        headingSelector: "h1, h2, h3, h4",
        // For headings inside relative or absolute positioned containers within content.
        hasInnerContainers: true,
    });
    tocbot.refresh();
}

function createID(el, ids) {
    let $ = jQuery;
    let id = $(el).text();
    id = id.toLowerCase().replaceAll(" ", "-");

    if (ids.indexOf(id) == -1) {
        return id;
    }

    let i = 0;
    while (ids.indexOf(id) != -1) {
        id = id + `-${i}`;
        i++;
    }

    return id;
}
