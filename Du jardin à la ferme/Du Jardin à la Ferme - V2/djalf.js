
$(function () {
    var apiClient = createApiClient(function(err){
        alert(err);
    });

    $('[contenteditable="true"]').each(function (index, elm) {
        elm = $(elm);
        elm.ckeditor();
    });
    
    function bindINPUT(type,mapper,operator,validator)
    {
        $(':not(:not(input) and :not(select))[data-djalf="' + type + '"]').each(function (index, elm) {
            elm = $(elm); 

            var applyChanges = $.debounce(1000, false, function () {
                operator(elm)
                .done(mapper);
            });
            function validateAndApply()
            {
                if (!validator || validator(elm))
                {
                    applyChanges();
                }
            }
            
            $(elm).on('change', validateAndApply);
            $(elm).on('keyup', validateAndApply);
        });
    }

    function bindEDITOR(type,mapper,operator)
    {
        $('[data-djalf="' + type + '"][contenteditable="true"]').each(function (index, elm) {
            elm = $(elm);

            var applyChanges = $.debounce(1000, false, function () {
                operator(elm,elm.ckeditor().editor)
                .done(mapper);
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

            if (elm.is("textarea")) {
                if (elm.val() != value)
                    elm.val(value);
            }
            else if (elm.is("input")) {
                if (elm.val() != value)
                    elm.val(value);
            }
            else if (elm.is("select")) {
                if (elm.val() != value)
                    elm.val(value);
            }
            else if (elm.is(".contentPanel"))
            {
                if (elm.is('[contenteditable="true"]'))
                {
                    if (elm.ckeditor().editor.getData() != value)
                        elm.ckeditor().editor.setData(value);
                }   
                else
                {
                    if (elm.html() != value)
                        elm.html(value);
                }
            }
            else
            {
                if (elm.text() != value)
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
        UPD_DOM_VAL('Produit-tarif', { 'id_produit': id_produit }, parseFloat(produit.tarif).toFixed(2).toString());
        UPD_DOM_VAL('Produit-unite', { 'id_produit': id_produit }, produit.unite);
        UPD_DOM_VAL('Produit-stocks_previsionnel', { 'id_produit': id_produit }, produit.stocks_previsionnel);
        UPD_DOM_VAL('Produit-stocks_courant', { 'id_produit': id_produit }, produit.stocks_courant);
    }

    bindINPUT('Produit-produit', UPD_Produit, function (elm) {
        return apiClient.produit_modifier_nom(elm.data("id_produit"), elm.val())
    });

    bindINPUT('Produit-unite', UPD_Produit, function (elm) {
        return apiClient.produit_modifier_unite(elm.data("id_produit"), elm.val())
    });

    bindINPUT(
        'Produit-tarif',
        UPD_Produit,
        function (elm) {
            return apiClient.produit_modifier_tarif(elm.data("id_produit"), elm.val())
        },
        function (elm) {
            var val = elm.val();
            fval = parseFloat(val).toFixed(2).toString();
            if (val !== fval) {
                elm.val(fval);
            }
            return true;
        }
    );
    
    bindEDITOR('Produit-description', UPD_Produit, function (elm, editor) {
        return apiClient.produit_modifier_description(elm.data("id_produit"), editor.getData())
    });


    function UPD_ProduitCommande(produitCommande) {
        UPD_Produit(produitCommande);
        
        var decimals=0;
        switch(produitCommande.unite)
        {
            case 'Kilogramme':
                decimals=3;
                break;
            default:
                decimals=0;
                break;
        }
        var id_element_commande = produitCommande.id_element_commande;
        UPD_DOM_VAL('ProduitCommande-id_commande', { 'id_element_commande': id_element_commande }, produitCommande.id_commande);
        UPD_DOM_VAL('ProduitCommande-quantite_commande', { 'id_element_commande': id_element_commande }, parseFloat(produitCommande.quantite_commande).toFixed(decimals).toString());
        UPD_DOM_VAL('ProduitCommande-quantite_reel', { 'id_element_commande': id_element_commande }, produitCommande.quantite_reel);
    }

    bindINPUT(
        'ProduitCommande-quantite_commande',
        UPD_ProduitCommande,
        function (elm) {
            return apiClient.produitcommande_modifier_quantite_commande(elm.data("id_commande"), elm.data("id_produit"), elm.val())
        },
        function (elm) {
            var val = elm.val();
            fval = parseFloat(val).toFixed(elm.data('decimals')).toString();
            if (val !== fval) {
                elm.val(fval);
            }
            return true;
        }
    );


});

