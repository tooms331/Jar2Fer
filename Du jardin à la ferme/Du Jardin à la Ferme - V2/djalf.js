
$(function () {
    var apiClient = createApiClient(function(err){
        alert(err);
    });

    $('[contenteditable="true"]').each(function (index, elm) {
        elm = $(elm);
        elm.ckeditor();
    });

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

            if (elm.is("textarea")) {
                if(!elm.data("isupdating"))
                    elm.val(value);
            }
            else if (elm.is("input")) {
                if (!elm.data("isupdating"))
                    elm.val(value);
            }
            else if (elm.is(".contentPanel"))
            {
                if (elm.is('[contenteditable="true"]'))
                {
                    if(!elm.data("isupdating"))
                        elm.ckeditor().editor.setData(value);
                }   
                else
                {
                    elm.html(value);
                }
            }
            else
            {
                elm.text(value);
            }
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
        var id_produit = produit.id_produit;
        UPD_DOM_VAL('Produit-id_categorie', { 'id_produit': id_produit }, produit.id_categorie);
        UPD_DOM_VAL('Produit-produit', { 'id_produit': id_produit }, produit.produit);
        UPD_DOM_VAL('Produit-categorie', { 'id_produit': id_produit }, produit.categorie);
        UPD_DOM_VAL('Produit-tarif', { 'id_produit': id_produit }, produit.tarif);
        UPD_DOM_VAL('Produit-unite', { 'id_produit': id_produit }, produit.unite);
        UPD_DOM_VAL('Produit-stocks_previsionnel', { 'id_produit': id_produit }, produit.stocks_previsionnel);
        UPD_DOM_VAL('Produit-stocks_courant', { 'id_produit': id_produit }, produit.stocks_courant);
    }

    function UPD_ProduitCommande(produitCommande) {
        UPD_Produit(produitCommande);

        var id_element_commande = produitCommande.id_element_commande;
        UPD_DOM_VAL('ProduitCommande-id_commande', { 'id_element_commande': id_element_commande }, produitCommande.id_commande);
        UPD_DOM_VAL('ProduitCommande-quantite_commande', { 'id_element_commande': id_element_commande }, produitCommande.quantite_commande);
        UPD_DOM_VAL('ProduitCommande-quantite_reel', { 'id_element_commande': id_element_commande }, produitCommande.quantite_reel);
    }


    $('input[data-djalf="ProduitCommande-quantite_commande"]').each(function (index, elm) {
        elm = $(elm);
        
        var applyChanges = $.debounce(1000, false, function () {
            elm.data("isupdating", true);
            elm.prop("disabled", true);
            apiClient.produitcommande_modifier_quantite_commande(elm.data("id_commande"), elm.data("id_produit"), elm.val())
            .done(UPD_ProduitCommande)
            .always(function () {
                elm.data("isupdating", false);
                elm.prop("disabled", false);
            });
        });

        $(elm).on('change', applyChanges);
    });

    $('[data-djalf="Produit-description"][contenteditable="true"]').each(function (index, elm) {
        elm = $(elm);

        var applyChanges = $.debounce(1000, false, function () {
            elm.data("isupdating", true);
            apiClient.produit_modifier_description(elm.data("id_produit"), elm.ckeditor().editor.getData())
            .done(UPD_Produit)
            .always(function () {
                elm.data("isupdating", false);
            });
        });

        elm.ckeditor().editor.on('change', applyChanges);
    });
});

