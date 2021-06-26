(function ($) {
    $(document).ready(function (e) {

        $(`.acr-key`).on(`click`, function (e) {
            e.preventDefault();

            let img = $(this).find(`.acr-col:last-child img`);
            let parent = $(this).parent();
            let currrentDeg = getRotationDegrees(img);
            let content = parent.find(`.acr-content`);

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
        }).trigger(`click`);

        $(`.list-items .list`).each(function (e) {
            let ind = $(this).index() + 1;
            $(this).prepend(`<div class="ordered-li-indicator">${ind}</div>`);
        });

        $(`.ul-col ul li`).prepend(
            `<div class="multi-col-li-indicator"><img src="${comparrot.rcs}/mt3/checkmark.svg" /></div>`
        );
    });
})(jQuery);



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
