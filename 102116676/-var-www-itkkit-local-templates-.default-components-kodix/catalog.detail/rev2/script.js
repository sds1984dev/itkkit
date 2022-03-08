/**
 * Created by:  KODIX 20.03.2015 14:43
 * Email:       support@kodix.ru
 * Web:         www.kodix.ru
 * Developer:   Kostin Denis
 */
"use strict";

var _createClass = (function() {
    function defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }
    return function(Constructor, protoProps, staticProps) {
        if (protoProps) defineProperties(Constructor.prototype, protoProps);
        if (staticProps) defineProperties(Constructor, staticProps);
        return Constructor;
    };
})();

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError("Cannot call a class as a function");
    }
}

/**
 * Class creating a block of sizes
 */
var SizeITK = (function() {
    /**
     * Create size block
     * @param {string} $el
     * @param {json} data
     */
    function SizeITK(_ref) {
        var $el = _ref["el"],
            data = _ref["data"],
            index = _ref["index"];
        _classCallCheck(this, SizeITK);

        // el init
        this.$el = $($el);
        // data init
        this.data = data;
        // index init
        if (index !== undefined) {
            this.index = index;
        } else {
            this.index = Math.floor(Math.random() * 100);
        }

        this.setUnits("cm");
        this.setSize("xs");

        this.initSizeSelect();
        this.initSizeUnits();
        this.renderImageLabels();
        this.renderTable();
        this.eventListeners();
    }

    /**
     * @param {string} size
     */

    _createClass(SizeITK, [
        {
            key: "setSize",
            value: function setSize(size) {
                this.size = size;
                this.$el.attr("data-size-size", this.size);
            }

            /**
             * @param {string} units
             */
        },
        {
            key: "setUnits",
            value: function setUnits(units) {
                this.units = units;
                this.$el.attr("data-size-units", this.units);
            }

            /**
             * Render label on image
             * @param {string} label
             * @param {json} sizeData
             * @param {string} unitsTxt
             */
        },
        {
            key: "renderLabel",
            value: function renderLabel(label, sizeData, unitsTxt) {
                if (sizeData[label] !== undefined) {
                    var labelData = sizeData[label][this.units];
                    this.$el.find("[data-size-" + label + "]").text(labelData + unitsTxt);
                }
            }

            /**
             * Render labels on image
             * @return {boolean}
             */
        },
        {
            key: "renderImageLabels",
            value: function renderImageLabels() {
                var sizeData = this.data[this.size];
                if (sizeData === undefined) return false;
                var unitsTxt = this.units === "cm" ? "cm" : '"';

                this.renderLabel("shoulder", sizeData, unitsTxt);
                this.renderLabel("arm", sizeData, unitsTxt);
                this.renderLabel("ptp", sizeData, unitsTxt);
                this.renderLabel("back", sizeData, unitsTxt);
                this.renderLabel("waist", sizeData, unitsTxt);
                this.renderLabel("rise", sizeData, unitsTxt);
                this.renderLabel("inside_leg", sizeData, unitsTxt);
                this.renderLabel("ankle", sizeData, unitsTxt);
                this.renderLabel("thigh", sizeData, unitsTxt);
            }

            /**
             * @param {string} label
             * @param {json} sizeData
             * @param {HTMLElement} $tr
             */
        },
        {
            key: "renderTd",
            value: function renderTd(label, sizeData, $tr) {
                if (sizeData[label] !== undefined) {
                    var labelData = sizeData[label][this.units];
                    $tr.find("[data-size-td=" + label + "]").text(labelData);
                }
            }

            /**
             * @param {string} index
             * @param {string} el
             * @return {boolean}
             */
        },
        {
            key: "renderTr",
            value: function renderTr(index, el) {
                var $tr = $(el);
                var size = $tr.attr("data-size-tr");
                var sizeData = this.data[size];
                if (sizeData === undefined) return false;

                this.renderTd("shoulder", sizeData, $tr);
                this.renderTd("arm", sizeData, $tr);
                this.renderTd("ptp", sizeData, $tr);
                this.renderTd("back", sizeData, $tr);
                this.renderTd("waist", sizeData, $tr);
                this.renderTd("rise", sizeData, $tr);
                this.renderTd("inside_leg", sizeData, $tr);
                this.renderTd("ankle", sizeData, $tr);
                this.renderTd("thigh", sizeData, $tr);
            }

            /**
             *
             */
        },
        {
            key: "renderTable",
            value: function renderTable() {
                $("[data-size-tr]").each(this.renderTr.bind(this));
            }

            /**
             *
             */
        },
        {
            key: "initSizeSelect",
            value: function initSizeSelect() {
                this.$el
                    .find("input:radio[name=size-select]")
                    .attr("name", "size-select-" + this.index);
                this.$el
                    .find("input:radio[value=" + this.size + "]")
                    .prop("checked", true);
            }

            /**
             *
             */
        },
        {
            key: "initSizeUnits",
            value: function initSizeUnits() {
                this.$el
                    .find("input:radio[name=size-units]")
                    .attr("name", "size-units-" + this.index);
                this.$el
                    .find("input:radio[value=" + this.units + "]")
                    .prop("checked", true);
            }

            /**
             *
             */
        },
        {
            key: "changeSize",
            value: function changeSize() {
                this.setSize(event.target.value);
                this.renderImageLabels();
            }

            /**
             *
             */
        },
        {
            key: "changeUnits",
            value: function changeUnits() {
                this.setUnits(event.target.value);
                this.renderImageLabels();
                this.renderTable();
            }

            /**
             *
             */
        },
        {
            key: "eventListeners",
            value: function eventListeners() {
                $("input:radio[name=size-select-" + this.index + "]").click(
                    this.changeSize.bind(this)
                );
                $("input:radio[name=size-units-" + this.index + "]").click(
                    this.changeUnits.bind(this)
                );
            }
        }
    ]);

    return SizeITK;
})();

$(function(){
    $(document).on('change','.product-sizes input',function(){
        var productID = parseInt(this.value);
        $('.kdxAddToCart').attr('data-product-id','');

        if(productID > 0){
            $('.kdxAddToCart').attr('data-product-id',productID);

            $('.pr_f_price div[data-product-price]').hide();
            $('.pr_f_price div[data-product-price="'+productID+'"]').show();
        }
    })

    //selectStyling('.form_select_v1', 'mes_select', BX.message('CHOOSE_SIZE'));
    //selectStyling('.form_select_v1', 'mes_select');
    $('.form_select_v1').trigger('change');

    $(document).on('kdxCartAddItem', function(){
        var rq = KDX.getQueryVariable('rq');
        if (rq!==undefined){
            try {rrApi.recomAddToCart(KDXSale.PROD_ID, {methodName: rq})} catch (e) {}
        }
    });
});