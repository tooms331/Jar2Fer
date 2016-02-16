
$(function () {
    var prodchange = {};
    function debounceproductchange(idproduit) {
        if (!prodchange[idproduit]) {
            prodchange[idproduit] = $.debounce(1000, false, function (elm, val) {
                elm.prop("disabled", true);
                apiClient.panier_modifier_element(idproduit, val)
                .always(function () {
                    elm.prop("disabled", false);
                });
            });
        }
        return prodchange[idproduit];
    }

    $('[data-inputtype="panier_qte_selector"]').on('change', function (elm) {
        elm = $(elm.target);
        debounceproductchange(elm.data("idproduit"))(elm, elm.val());
    })
});