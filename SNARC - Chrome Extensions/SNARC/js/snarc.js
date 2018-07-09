if (!window.snarc) window.snarc = {};

jQuery.extend(window.snarc,
{
    buildSidebar: function(url)
    {

        if (!window.snarc.CONSTANTS.is_snarc_in_action)
        {

            window.snarc.CONSTANTS.is_snarc_in_action = true;
            var side_bar = $(window.snarc.DOM.sidebar).appendTo($('body'));

            $(window.snarc.DOM.entityInspector).appendTo($('body'));
            
            var toolbar = $(window.snarc.DOM.toolbar).appendTo(side_bar);
            $(window.snarc.DOM.shutdownSNARC).appendTo(toolbar);
            $(window.snarc.DOM.loadingFunctionStatus).appendTo(side_bar);
            $(window.snarc.DOM.laodingSNARCStatus).appendTo(side_bar);
            $(window.snarc.DOM.socialAggregationResult).appendTo(side_bar);
            $(window.snarc.DOM.docummentAnnotationResult).appendTo(side_bar);

            window.snarc.attachControl();
            window.snarc.semanticDocumentCall(url);

        }
        else window.snarc.FUNCTIONS.hideSNARC();

        return true;
    },

    attachControl: function()
    {
        //Attaching the hide functions
        $(window.snarc.DOM.hideSNARCButton).on('click', function()
        {
            window.snarc.FUNCTIONS.hideSNARC();
        });
        //Attaching closing SNARC action
        $(window.snarc.DOM.closeSNARC).on('click', function()
        {
            window.snarc.FUNCTIONS.removeSNARC();
        });
        //Controlling the visibility of highlighted entities in document
        $(window.snarc.DOM.toggleEntitiesVisibility).on('click', function()
        {
            $(window.snarc.DOM.highlightedEntity).toggleClass(window.snarc.DOM.highlightedEntityOff);
            if ($(this).hasClass(window.snarc.DOM.entitiesVisibleIcon))
            {
                $(this).removeClass(window.snarc.DOM.entitiesVisibleIcon).addClass(window.snarc.DOM.entitiesHiddenIcon);
                window.snarc.CONSTANTS.hideEntities = true;
                $('body:not(.snarc-sidebar)').removeHighlight();
            }
            else
            {
                $(this).removeClass(window.snarc.DOM.entitiesHiddenIcon).addClass(window.snarc.DOM.entitiesVisibleIcon);
                window.snarc.CONSTANTS.hideEntities = false;
                window.snarc.attachEntities();
            }
        });
        //Adding handlers to switch between social news view and document annotation
        $(window.snarc.DOM.SNARCActiveArea).on('click', function()
        {
            if ($(this).hasClass(window.snarc.DOM.SNARCAnottationIcon))
            {
                window.snarc.FUNCTIONS.changeScreenIconAnottationToSocial();
                window.snarc.FUNCTIONS.changeScreensAnottationToSocial();
            }
            else
            {
                window.snarc.FUNCTIONS.changeScreenIconSocialToAnnotation();
                window.snarc.FUNCTIONS.changeScreensSocialToAnottation();
            }
        });
        //handling the hiding of the entity pop when clicking anywhere else in the document
        $(document).mouseup(function(e)
        {
            var container = $(window.snarc.DOM.entityPopup);
            if (container.has(e.target).length === 0) container.hide();
        });
    },

    attachEntities: function()
    {
        var entities = window.snarc.VIEWS.Semanticdocument.entities;
        $.get(chrome.extension.getURL('js/templates/entity-view.tpl'), function(template)
        {
            $(template).appendTo($('body'));
            $(entities).each(function(i, v)
            {
                var entity = this.entity;
                $(this.text).each(function(i, v)
                {
                    $('body:not(.snarc-sidebar)').highlight(' ' + v + ' ', entity);
                });
            });
            window.snarc.buildEntityInspector();
        });
    },

    buildEntityInspector: function()
    {

        var entityModel = Backbone.Model.extend(
        {
            defaults: window.snarc.VIEWS.Semanticdocument.entities[0],
            initialize: function()
            {
                _.bindAll(this, "update");
                this.bind('change', this.update);
            },
            update: function() {}
        });

        //initialize a backbone model based on the defined settings
        var entityModel = new entityModel();
        //initialize a backbone view and bind it to the model
        var entityView = Backbone.View.extend(
        {
            initialize: function()
            {
                entityModel.on('change', this.render, this);
            },
            el: $(window.snarc.DOM.entityPopup),
            render: function()
            {
                var html = $(window.snarc.DOM.entityPopupTemplate).tmpl(entityModel.toJSON());
                $(this.el).html(html);
            }
        });
        //create the view variable and render it
        var SNARC_EntityView = new entityView();
        SNARC_EntityView.render();
        //attach the funtion when clicking on an entity on the page
        $(window.snarc.DOM.highlightedEntity).on('mouseover', function(e)
        {
            var popupCoordinates = $(this).offset();
            var popUp = $(window.snarc.DOM.entityPopup);
            var highlightValue = $(this).attr("data-entity");
            var newModel = _.find(window.snarc.VIEWS.Semanticdocument.entities, function(v, k)
            {
                if (v.entity === highlightValue) return v;
            });

            entityModel.set(newModel);
            if (!window.snarc.CONSTANTS.hideEntities) $(window.snarc.DOM.entityPopup).show();
            else $(window.snarc.DOM.entityPopup).css('display', 'none');
            $(window.snarc.DOM.entityPopup).css(
            {
                'left': popupCoordinates.left - (popUp.width() / 2),
                'top' : popupCoordinates.top - popUp.height()
            })
        });

    },

    semanticDocumentCall: function(url)
    {
        jQuery.ajax(
        {
            url: window.snarc.URLS.SNARC_service_url,
            type: 'POST',
            dataType: 'json',
            data: { url: url },
            success: function(data, textStatus, xhr)
            {
                console.log(data);
                window.snarc.VIEWS.Semanticdocument = data;
                window.snarc.VIEWS.keywords = _.first(data.keywords, 2);
                window.snarc.attachEntities();
                $.get(chrome.extension.getURL('js/templates/document-annotations.tpl'), function(template)
                {

                    $.tmpl(template, data).appendTo(window.snarc.DOM.documentAnnotationScreen);
                    $(window.snarc.DOM.statusMessage).text(window.snarc.MESSAGES.fetchingSocial);
                    

                    window.snarc.FUNCTIONS.hideSNARCLoader();
                    window.snarc.socialAggreagatorCall(url, data);

                    $(window.snarc.DOM.SNARC).niceScroll( { cursorcolor: "#000", horizrailenabled: false });

                });
            },
            error: function(xhr, textStatus, errorThrown)
            {
                $(window.snarc.DOM.SNARCLoader).text(window.snarc.MESSAGES.failedAJAXCall).css('color', '#d73532');
                console.log(xhr);
            }
        });
    },

    socialAggreagatorCall: function(url, data)
    {
        jQuery.ajax(
        {
            url: window.snarc.URLS.SNARC_social_url,
            type: 'POST',
            dataType: 'json',
            data: { url: url, d: JSON.stringify(data) },
            success: function(data, textStatus, xhr)
            {
                console.log(data);
                $.get(chrome.extension.getURL('js/templates/social-bar.tpl'), function(template)
                {
                    $.tmpl(template, data).appendTo(window.snarc.DOM.relatedSocialResultsScreen);

                    window.snarc.FUNCTIONS.hideSNARCLoader();
                    window.snarc.FUNCTIONS.hideStatusMessage();
                    window.snarc.FUNCTIONS.changeScreenIconAnottationToSocial();
                    window.snarc.FUNCTIONS.changeScreensAnottationToSocial();
                    window.snarc.FUNCTIONS.addModal();
                    window.snarc.FUNCTIONS.attachIntentsHandler();

                });
            },
            error: function(xhr, textStatus, errorThrown)
            {
                $(window.snarc.DOM.SNARCLoader).text(window.snarc.MESSAGES.failedAJAXCall).css('color', '#d73532');
                console.log(xhr);
            }
        });
    }
});