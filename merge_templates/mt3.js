(function ($) {
    $(document).ready(function (e) {
        mt3Toc();

        $(".bi-col:nth-child(2)").owlCarousel({
            loop: true,
            items: 2,
            margin: 10,
            nav: true,
            responsiveClass: false,
            //Autoplay
            autoPlay: 2000,
            stopOnHover: true,

            responsive: {
                0: {
                    items: 1,
                    nav: true,
                },
                600: {
                    items: 2,
                    nav: false,
                },
                1000: {
                    items: 3,
                    nav: true,
                    loop: false,
                },
            },
        });
        var owl = $(".bi-col:nth-child(2)").data("owlCarousel");

        $(`.bi-col:nth-child(1) img`).on(`click`, function (e) {
            owl.prev();
        });
        $(`.bi-col:nth-child(3) img`).on(`click`, function (e) {
            owl.next();
        });

        // setInterval(() => {
        //     owl.next();
        // }, 1500);
        // owl.buildControls();

        $(`.toc-key`).on(`click`, function (e) {
            e.preventDefault();

            let img = $(this).find(`.toc-col:last-child img`);
            let currrentDeg = getRotationDegrees(img);
            let content = $(`.toc-content`);

            if (currrentDeg == 0) {
                img.css({
                    transform: `rotate(180deg)`,
                });
                content.slideUp(300);
            } else if (currrentDeg == 180) {
                img.css({
                    transform: `rotate(0deg)`,
                });
                content.slideDown(300);
            }
        });

        $(`ul.ordered li`).each(function (e) {
            let ind = $(this).index() + 1;
            $(this).prepend(`<div class="ordered-li-indicator">${ind}</div>`);
        });

        $(`.ul-col ul li`).prepend(
            `<div class="multi-col-li-indicator"><img src="${comparrot.rcs}/mt3/checkmark.svg" /></div>`
        );
    });
})(jQuery);

function mt3Toc() {
    let $ = jQuery;
    if ($(`.mt3-toc`).length == 0) {
        return;
    }

    let ids = [];

    // $(`.page-content-section h1`).hide();
    // return;

    let selectors = $(
        `.page-content-section > h1, 
        .page-content-section > h2, 
        .page-content-section > h3,
        .page-content-section > h4`
    );

    // for(let i = 0; i < selectors.length; i++){
    //     let item = selectors.eq(i);
    //     let id = createID(item, ids);
    //     ids.push(id);
    //     item.attr(`id`, id);
    // }
    selectors.each(function () {
        let id = createID($(this), ids);
        ids.push(id);
        $(this).attr(`id`, id);
    });

    tocbot.init({
        // Where to render the table of contents.
        tocSelector: ".toc-content",
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

function getRotationDegrees(obj) {
    var matrix =
        obj.css("-webkit-transform") ||
        obj.css("-moz-transform") ||
        obj.css("-ms-transform") ||
        obj.css("-o-transform") ||
        obj.css("transform");
    if (matrix !== "none") {
        var values = matrix.split("(")[1].split(")")[0].split(",");
        var a = values[0];
        var b = values[1];
        var angle = Math.round(Math.atan2(b, a) * (180 / Math.PI));
    } else {
        var angle = 0;
    }
    return angle < 0 ? angle + 360 : angle;
}
