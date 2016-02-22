
$(function () {
    var apiClient = createApiClient(function(err){
        alert(err);
    });
    $.fn.changeVal = function (v) {
        return $(this).val(v).trigger("change");
    }

    function DOM_GET(jelm) {

        if (jelm.is("textarea")) {
            return jelm.val();
        }
        else if (jelm.is("input")) {
            return jelm.val();
        }
        else if (jelm.is("select")) {
            return jelm.val();
        }
        else if (jelm.is(".contentPanel")) {
            if (jelm.is('[contenteditable="true"]')) {
                return jelm.ckeditor().editor.getData();
            }
            else {
                return jelm.html();
            }
        }
        else {
            return jelm.text();
        }
    };

    function DOM_SET(jelm, value) {
        if (jelm.data("decimals")) {
            var decimals = jelm.data("decimals");
            value = parseFloat(value).toFixed(decimals).toString();
        }

        if (jelm.is("textarea")) {
            if (jelm.val() != value)
                jelm.changeVal(value);
        }
        else if (jelm.is("input")) {
            if (jelm.val() != value)
                jelm.changeVal(value);
        }
        else if (jelm.is("select")) {
            if (jelm.val() != value)
                jelm.changeVal(value);
        }
        else if (jelm.is(".contentPanel")) {
            if (jelm.is('[contenteditable="true"]')) {
                if (jelm.ckeditor().editor.getData() != value)
                    jelm.ckeditor().editor.setData(value);
            }
            else {
                if (jelm.html() != value)
                    jelm.html(value);
            }
        }
        else {
            if (jelm.text() != value)
                jelm.text(value);
        }
    };

    function checkDecimals() {
        var elm = $(this);
        var val = DOM_GET(elm);
        var decimals = elm.data("decimals");
        fval = parseFloat(val).toFixed(decimals).toString();
        DOM_SET(elm, fval);
        return true;
    }

    function resizeInput() {
        var elm = $(this);
        elm.attr('size', elm.val().length);
    }

    $('input[type="text"]')
        .keyup(resizeInput)
        .change(resizeInput)
        .each(resizeInput);
    $('input[type="number"]')
        .keyup(resizeInput)
        .change(resizeInput)
        .each(resizeInput);

    $('[data-decimals]')
        .change(checkDecimals)
        .each(checkDecimals);
    
    $('[contenteditable="true"]').each(function () {
        elm = $(this);
        elm.ckeditor();
    });
    
    function bindINPUT(type,operator)
    {
        $(':not(:not(input) and :not(select))[data-djalf="' + type + '"]').each(function (index, elm) {
            elm = $(elm); 

            var applyChanges = $.debounce(1000, false, function () {
                operator(elm);
            });
            
            elm
                .change(applyChanges)
                .keyup(applyChanges);
        });
    }

    function bindEDITOR(type,operator)
    {
        $('[data-djalf="' + type + '"][contenteditable="true"]').each(function (index, elm) {
            elm = $(elm);

            var applyChanges = $.debounce(1000, false, function () {
                operator(elm,elm.ckeditor().editor);
            });

            elm.ckeditor().editor.on('change', applyChanges);
        });
    }

    function UPD_DOM_VAL(type,selector,value)
    {
        var sel = "";
        for (var property in selector) {
            if (selector.hasOwnProperty(property)) {
                sel += '[data-' + property + '="' + selector[property].toString() + '"]';
            }
        }

        var q ='[data-djalf="'+type+'"]'+sel;

        $(q).each(function(idx,elm){
            elm = $(elm);

            DOM_SET(elm, value);
        });
    }

    function ARRAY_WALKER(cb)
    {
        return function(array)
        {
            for(var i = 0;i<arr.length;i++)
                cb(array[i]);
        }
    }

    function UPD_Produit(produit) {
        var selectors = { id_produit: produit.id_produit };
        UPD_DOM_VAL('Produit-id_categorie', selectors, produit.id_categorie);
        UPD_DOM_VAL('Produit-produit', selectors, produit.produit);
        UPD_DOM_VAL('Produit-categorie', selectors, produit.categorie);
        UPD_DOM_VAL('Produit-prix_unitaire_ttc', selectors, produit.prix_unitaire_ttc);
        UPD_DOM_VAL('Produit-unite', selectors, produit.unite);
        UPD_DOM_VAL('Produit-tva', selectors, produit.tva);
        UPD_DOM_VAL('Produit-stocks_previsionnel', selectors, produit.stocks_previsionnel);
        UPD_DOM_VAL('Produit-stocks_courant', selectors, produit.stocks_courant);
    }

    function UPD_ProduitCommande(produitCommande) {
        UPD_Produit(produitCommande);
        var selectors = { id_commande: produitCommande.id_commande, id_produit: produitCommande.id_produit };

        UPD_DOM_VAL('ElementCommande-id_commande', selectors, produitCommande.id_commande);
        UPD_DOM_VAL('ElementCommande-quantite_commande', selectors, produitCommande.quantite_commande);
        UPD_DOM_VAL('ElementCommande-quantite_reel', selectors, produitCommande.quantite_reel);
        UPD_DOM_VAL('ElementCommande-prix_total_element_ttc', selectors, produitCommande.prix_total_element_ttc);
        UPD_DOM_VAL('ElementCommande-prix_total_element_ht', selectors, produitCommande.prix_total_element_ht);
        UPD_DOM_VAL('ElementCommande-tva_total_element', selectors, produitCommande.tva_total_element);
    }

    function UPD_Commande(commande) {
        var selectors = { id_commande: commande.id_commande };
        UPD_DOM_VAL('Commande-prix_total_commande_ttc', selectors, commande.prix_total_commande_ttc);
        UPD_DOM_VAL('Commande-prix_total_commande_ht', selectors, commande.prix_total_commande_ht);
        UPD_DOM_VAL('Commande-tva_total_commande', selectors, commande.tva_total_commande);
    }

    function UPD_ProduitCommandeDetail(produitCommandeDetail) {
        UPD_ProduitCommande(produitCommandeDetail);
        UPD_Commande(produitCommandeDetail);
    }

    bindINPUT('Produit-produit', function (elm) {
        apiClient
            .produit_modifier_nom(elm.data("id_produit"), elm.val())
            .done(UPD_Produit);
    });

    bindINPUT('Produit-unite', function (elm) {
        apiClient
            .produit_modifier_unite(elm.data("id_produit"), elm.val())
            .done(UPD_Produit);
    });

    bindINPUT('Produit-prix_unitaire_ttc', function (elm) {
        apiClient
            .produit_modifier_prix_unitaire_ttc(elm.data("id_produit"), elm.val())
            .done(UPD_Produit);
    });
    
    bindEDITOR('Produit-description', UPD_Produit, function (elm, editor) {
        apiClient
            .produit_modifier_description(elm.data("id_produit"), editor.getData())
            .done(UPD_Produit);
    });

    bindINPUT('ElementCommande-quantite_commande', function (elm) {
        apiClient
            .produitcommande_modifier_quantite_commande(elm.data("id_commande"), elm.data("id_produit"), elm.val())
            .done(UPD_ProduitCommandeDetail);
    });

});

