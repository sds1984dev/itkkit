/**
 * Created by Alexander Samakin on 14.07.14.
 */

window.KDXSale = function () {
};

KDXSale.Option = function (metod, name, value, is_new) {
    if (typeof this.options === "undefined") {
        this.options = {
            NeedConfirmRemoveFromCart:true,
            RefreshSmallCart: true,
            RefreshSmallWish: true
        }
    }
    switch (metod) {
        case 'getOption':
            return this.options[name];
            break;

        case 'setOption':
            if (is_new !== true && typeof this.options[name] == "undefined") {
                return false;
            }
            this.options[name] = value;
            return true;
            break;
        default :
            return undefined;
            break;
    }
}
KDXSale.setOption = function (name, value, is_new) {
    return KDXSale.Option('setOption', name, value, is_new)
}
KDXSale.getOption = function (name) {
    return KDXSale.Option('getOption', name)
}


/******************РАБОТА С КОРЗИНОЙ*************************************/
KDXSale.addToCart = function (product_id, quantity, fuser_id, refresh_small) {
    if (typeof product_id === "undefined" || !product_id) {
        console.error("addToCart: product id is undefined");
        return false;
    }
    if (typeof quantity === "undefined" || parseInt(quantity) <= 0) {
        quantity = 1;
    }
    if (typeof fuser_id === "undefined" || parseInt(fuser_id) <= 0) {
        fuser_id = false;
    }
    if (typeof refresh_small === "undefined") {
        refresh_small = true;
    }
    KDX.ajax("/ajax/cart/addItem.php", {
        type: "post",
        data: {
            product_id: product_id,
            quantity: quantity,
            fuser_id: fuser_id,
            refresh_small: KDXSale.getOption("RefreshSmallCart")
        },
        dataType: "json",
        success: function (data) {
            if (!data) {
                console.error("failed add to cart product id=" + product_id + " quantity=" + quantity + " fuser_id=" + fuser_id);
                return false;
            } else {
                console.log(data);
            }
            if (typeof data.CART_MINI !== "undefined")
                $(".kdx_cart_small").replaceWith(data.CART_MINI);
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxCartAddItem', [jqXHR, product_id, quantity, fuser_id, refresh_small]);

        }
    });
}

KDXSale.removeFromCart = function (record_id) {
    if (typeof record_id === "undefined" || parseInt(record_id) <= 0) {
        return false;
    }
    KDX.ajax("/ajax/cart/removeItem.php", {
        type: "post",
        data: {
            record_id: record_id,
            refresh_small: KDXSale.getOption("RefreshSmallCart")
        },
        dataType: "json",
        success: function (data) {
            if (!data) {
                console.error("failed remove from cart record id=" + record_id);
                return false;
            }
            if (typeof data.CART_MINI !== "undefined")
                $(".kdx_cart_small").replaceWith(data.CART_MINI);

            $("[data-item-id=" + record_id + "]").closest(".cart_item").remove();
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxCartRemoveItem', [jqXHR, record_id]);
        }

    });
}

KDXSale.applyCouponCode = function (code) {
    KDX.ajax("/ajax/cart/applyCoupon.php", {
        type: "post",
        data: {
            code: code
        },
        dataType: "json",
        success: function (data) {
            if (!data) {
                console.error("failed apply coupon code=" + code);
                return false;
            }
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxCartApplyCoupon', [jqXHR, code]);
        }
    });
}

KDXSale.clearCart = function () {
    KDX.ajax("/ajax/cart/clearCart.php", {
        type: "post",
        data: {},
        dataType: "json",
        success: function (data) {
            if (!data) {
                console.error("failed clear cart");
                return false;
            }
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxCartClear', [jqXHR]);
            document.location.reload();
        }
    });
}

KDXSale.updateCart = function (dont_show_preload) {


    KDX.ajax("/ajax/cart/updateCart.php", {
        type: "post",
        data: {},
        dataType: "json",
        dont_show_preload: dont_show_preload,
        success: function (data) {
            if (!data) {
                console.error("failed update cart");
                return false;
            }

            if (typeof data.CART_MINI !== "undefined")
                $(".kdx_cart_small").replaceWith(data.CART_MINI);
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxCartUpdate', [jqXHR]);
        }
    });
}

/******************РАБОТА СО СПИСКОМ ИЗБРАННОГО*************************************/
KDXSale.addToWishList = function (product_id) {
    KDX.ajax("/ajax/wishlist/addToWishList.php", {
        type: "post",
        data: {
            product_id: product_id,
            refresh_small: KDXSale.getOption("RefreshSmallWish")
        },
        dataType: "json",
        success: function (data) {
            if (!data || data.STATUS == "FAIL") {
                console.error("failed add to wish");
                return false;
            }
            if (typeof data.WISH_MINI !== "undefined")
                $(".kdx_wish_small").replaceWith(data.WISH_MINI);

            $('.kdxAddToWishList[data-product-id="'+product_id+'"] .content').text("В избранном");
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxWishlistAddItem', [jqXHR, product_id]);
        }
    });
}

KDXSale.removeFromWishList = function (product_id) {
    KDX.ajax("/ajax/wishlist/removeFromWishList.php", {
        type: "post",
        data: {
            item_id: product_id,
            refresh_small: KDXSale.getOption("RefreshSmallWish")
        },
        dataType: "json",
        success: function (data) {
            if (!data || data.STATUS == "FAIL") {
                console.error("failed remove from wish");
                return false;
            }
            if (typeof data.WISH_MINI !== "undefined")
                $(".kdx_wish_small").replaceWith(data.WISH_MINI);
            $('.kdxAddToWishList[data-product-id="'+product_id+'"] .content').text("Добавить в избранное");
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxWishlistRemoveItem', [jqXHR, product_id]);
        }
    });
}


/**************************АДРЕСА ДОСТАВКИ***********************************/
KDXSale.getAddressCities = function (country_id) {
    country_id = parseInt(country_id);
    if (!country_id) {
        console.error("failed get cities list. country_id is undefined");
        return false;
    }
    var cities = [];
    KDX.ajax("/ajax/addresses/getCities.php", {
        type: "post",
        data: {
            country_id: country_id
        },
        async: false,
        success: function (data) {
            var tmp1 = data.split("::");
            $.each(tmp1, function (i, string) {
                var tmp2 = string.split("##");
                if (typeof tmp2[0] != "undefined" && typeof tmp2[1] != "undefined") {
                    cities.push(string);
                }
            });
        },
        complete: function (jqXHR) {
            $(document).trigger('kdxAddressCities', [jqXHR, country_id]);
        }
    });
    return cities;
}

KDXSale.createAddress = function (fields, options) {
    if (typeof (fields) == "undefined" || fields.length == 0)
        return false;
    if (typeof options === "undefined") {
        options = {};
    }
    options.type = "post";
    options.data = fields;
    KDX.ajax("/ajax/addresses/createAddress.php", options);
}

KDXSale.removeAddress = function (profile_id, options) {
    if (typeof (profile_id) == "undefined" || parseInt(profile_id) <= 0)
        return false;
    if (typeof options === "undefined") {
        options = {};
    }
    options.type = "post";
    options.data = {
        profile_id: profile_id
    };
    KDX.ajax("/ajax/addresses/removeAddress.php", options);
}

/**
 * Отмена заказа
 * @param order_id ID заказа
 * @param reason Причина отмены заказа (необязательно)
 * @returns {boolean}
 */
KDXSale.cancelOrder = function (order_id, reason) {
    if (!order_id) {
        console.error("Failed to cancel order. Order ID not set.");
        return false;
    }
    KDX.ajax("/ajax/cancelOrder.php", {
        type: "post",
        dataType: "json",
        data: {
            "order_id": order_id,
            "reason": reason
        },
        success: function (serverData) {
            var btnCancelOrder = $(".kdxOrderCancel[data-order-id=" + order_id + "]");
            var labelOrderStatus = $(".kdxOrderStatus[data-order-id=" + order_id + "]");
            var paymentOrderForm = $(".kdxOrderPay[data-order-id=" + order_id + "]");

            switch (serverData.STATUS) {
                case "OK":
                    labelOrderStatus.text("Отменен");
                    btnCancelOrder
                        .trigger("click") // hide popup
                        .removeClass("txt_red")
                        .addClass("txt_gray")
                        .text("Заказ отменен");
                    btnCancelOrder
                        .parent(".popup-order-cancel")
                        .find(".sub")
                        .remove(); // remove popup
                    paymentOrderForm.remove();
                    break;
                case "FAIL":
                    console.error("Failed to canceling order. Something wrong.");
                    break;
            }
        },
        complete: function (jqXHR) {
            $(document).trigger("kdxCancelOrder", [jqXHR, order_id, reason]);
        }
    });
}

/**
 * устанавливаем стандартные обработчики
 */
KDXSale.jquery_binds = function () {
    $(document).ready(function () {
        $(document).on('click', '.kdxAddToCart', function () {
            var product_id = $(this).attr("data-product-id");
            if (typeof product_id === "undefined" || !product_id) {
                product_id = $("select.kdxProductSelect").val();
            }
            if(parseInt(product_id) < 1 || isNaN(parseInt(product_id)))
            {
                KDX.Informers.addError(BX.message('CHOOSE_SIZE'))
            }

            var quantity = $(this).attr("quantity");
            KDXSale.addToCart(product_id, quantity);
            return false;
        });

        $(document).on('keyup', '.kdxQuantity', function () {
            var item_id = $(this).attr("id").replace("kdxQuantity", "");
            if (item_id) {
                $(".kdxAddToCart[data-product-id=" + item_id + "]").attr("quantity", $(this).val());
            }
        });

        $(document).on('click', '.kdxDeleteFromCart', function () {
            var item_id = $(this).attr("data-product-id");
            if (item_id) {
                var confirmed = true;
                if(KDXSale.getOption('NeedConfirmRemoveFromCart') === true){
                    confirmed = !!confirm('Подтвердите удаление из корзины')
                }
                if(confirmed){
                    KDXSale.removeFromCart(item_id);
                }
            }
            return false;
        });


        $(document).on('click', '.kdxApplyCoupon', function () {
            var code = $("#kdxCouponCode").val();
            KDXSale.applyCouponCode(code);
            return false;
        });

        $(document).on('click', '.kdxClearCart', function () {
            KDXSale.clearCart();
            return false;
        });

        $(document).on('click', '.kdxAddToWishList', function (event) {
            if ($(this).hasClass("added")) {
                return false;
            }
            $(this).addClass("added");
            var product = $(this).attr("data-product-id");
            if ($.inArray(product, KDXSale.wishlist) < 0) {
                KDXSale.wishlist[KDXSale.wishlist.length] = product;
            }
            if (product) {
                KDXSale.addToWishList(product);
            }
            return false;
        });

        $(document).on('click', '.kdxRemoveFromWishList', function () {
            var product = $(this).attr("data-product-id");
            if (product) {
                KDXSale.removeFromWishList(product);
                $(this).closest(".kdxAddToWishList").removeClass("added");
                var position = KDXSale.wishlist.indexOf(product);
                if (position >= 0) {
                    KDXSale.wishlist.splice(position);
                }
            }
            return false;
        });

        $(document).on('submit', '.kdxFormOrderCancel', function () {
            var order_id = $(this).find("[name=order_id]").val();
            var reason = $(this).find("[name=cancel_reason]").val();
            KDXSale.cancelOrder(order_id, reason);
            return false;
        });

    });

}

KDXSale.init = function () {
    KDXSale.jquery_binds();
}
KDXSale.wishlist = [];
KDXSale.init();
