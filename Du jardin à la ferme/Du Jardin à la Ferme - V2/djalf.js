function debounceStore(Callback) {
    var store = {};
    return function (id) {
        if (!store[id]) {
            store[id] = $.debounce(1000, false, Callback);
        }
        return store[id];
    }
}


$(function () {
    
    var debounceproductqtechange = debounceStore(
        function (elm) {
            elm.prop("disabled", true);
            apiClient.panier_modifier_element(elm.data("idproduit"), elm.val())
            .always(function () {
                elm.prop("disabled", false);
            });
        }
    );
    
    $('[data-inputtype="panier_qte_selector"]').on('change', function (elm) {
        elm = $(elm.target);
        debounceproductqtechange(elm.data("idproduit"))(elm);
    })



    var debounceproductdescchange = debounceStore(
        
    );

    $('[data-inputtype="Produit-Description-Editor"]').each(function (index, elm) {
        elm = $(elm);
        var idproduit=elm.data("idproduit");
        var editor = elm.ckeditor().editor;

        var applyChanges = $.debounce(1000, false, function () {
            apiClient.produit_modifier_description(idproduit, editor.getData())
            .always(function () {
            });
        });

        editor.on('change', function () {
            applyChanges();
        });
        elm.on('blur', function () {
            applyChanges();
        });
        
    });
});

