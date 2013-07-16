var Oktolab = {};

Oktolab.Calendar = function Calendar(container) {
    'use strict';

    this.options = {
        'container': container,
        'startdate': new Date(),
        'eventSrcPath': 'gone.json',
        'configSrcPath': 'gone.json' // timeblocks and inventory
    };

    this.data = {
        container: AJS.$(this.options.container),
    };

    this.render = function () {
        this.renderBasicCalendar();
        this.renderInventory();

        var wrapperLength = this.data.container.width() - this.data.inventory.width(), // set height for wrapper
            $date = this.options.startdate,
            $blocks = '',
            i = 0;

        /*$wrapper.height($inventory.outerHeight(true));

        // draw calendar background
        for (i; i <= (wrapperLength - 300); i += 100) {
            $blocks = $blocks + this.getBlockForDate($date);
            $date.setDate($date.getDate() + 1);
        }
        $wrapper.append($blocks); */

        /*/ draw events (for and so on ...)
        var position = AJS.$('#test-element').offset();
        $wrapper.append('<div id="testevent" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent');
        $testevent.offset({ top: position.top });
        $testevent.width(300);
        $testevent.height(29);

        // draw events (for and so on ...)
        var position = AJS.$('#test-element2').offset();
        $wrapper.append('<div id="testevent2" style="background-color: red;">aaaaaaa</div>');
        $testevent = AJS.$('#testevent2');
        $testevent.offset({ top: position.top, left: 430 });
        $testevent.width(300);
        $testevent.height(29);*/
    };

    this.getBlockForDate = function (date) {
        return '<div class="calendar-date"><div class="calendar-headline"><span class="calendar-title">' + date.getDate() + '.' + (date.getMonth() + 1) + '</span><div class="calendar-timeblock">09-12</div><div class="calendar-timeblock">17-20</div></div></div>';
    };

    this.getInventory = function () {
        var $json       = AJS.$.parseJSON('{"stuff":["item","item","item"],"some more stuff":["item bar","item foo","item baz"]}'),
            $key        = null, // will be used to iterate itemgroups
            $item       = null, // will be used to iterate items
            $inventory  = '';

        for ($key in $json) {
            //console.log($json[$key].length);
            $inventory = $inventory +
                    '<div class="calendar-inventory-group">' +
                        '<strong>' + $key + '</strong>' +
                        '<ul>';

            for ($item in $json[$key]) {
                //console.log($json[$key][$item]);
                $inventory = $inventory + '<li><a href="#">' + $json[$key][$item] + '</a></li>';
            }


            $inventory = $inventory + '</ul>' + '</div>';
            /*<div class="calendar-inventory-group">
            <strong>Stuff</strong>
            <ul>
                <li><a href="#">Item Foo</a></li>
                <li><a href="#">Item Bar</a></li>
                <li><a href="#">Item Baz</a></li>
                <li><a href="#" id="test-element">Item Cool</a></li>
                <li><a href="#" id="test-element2">Item Foo</a></li>
            </ul>
        </div>*/
        }

        console.log($inventory);

        return $inventory;
    };

    this.renderInventory = function () {
        var $inventory = this.getInventory();
        console.log($inventory);
        this.data.inventory.append($inventory);
    };

    this.renderBasicCalendar = function () {
        this.data.container.append('<div class="calendar-inventory"></div><div class="calendar-wrapper"></div>');

        this.data.inventory = this.data.container.children('.calendar-inventory');
        this.data.wrapper = this.data.container.children('.calendar-wrapper');
    }
};