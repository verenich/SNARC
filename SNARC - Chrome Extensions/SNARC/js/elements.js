if (!window.snarc) window.snarc = {};

jQuery.extend(window.snarc,
{
    'DOM': {
        SNARC:'.snarc-sidebar',
        sidebar: '<div class="snarc-sidebar"><span class="snarc-closeHandler"></span></div>',
        toolbar: '<div class="snarc-toolbar"><i class="active_area icon-th-list"></i><i class="toggleEntities icon-eye"</i></div>',
        statusMessage: '.snarc-status',
        shutdownSNARC: '<span class="SNARCshutdown">CLOSE</span>',
        loadingFunctionStatus: '<div class="snarc_message_bar"><div class="snarc-status">Annotating Document ...</div><img class="spinner" src="' + chrome.extension.getURL('icons/social-loader.gif') + '"/></div>',
        laodingSNARCStatus: '<div class="loading"><i class="icon-signal"></i>Loading Social Sidebar<img src="' + chrome.extension.getURL('icons/ajax-loader.gif') + '"/></div>',
        SNARCLoader: '.snarc-sidebar .loading',
        socialAggregationResult: '<ul id = "realted_social" class="social_stream"></ul>',
        docummentAnnotationResult: '<ul id = "document_annotations" class="social_stream"></ul>',
        hideSNARCButton: '.snarc-closeHandler',
        closeSNARC: '.SNARCshutdown',
        documentAnnotationScreen: '#document_annotations',
        relatedSocialResultsScreen : '#realted_social',
        entityInspector: '<div class="snarc-entity-view"></div>',
        highlightedEntity: '.SNARCHighlight',
        highlightedEntityOff: 'SNARCHighlightOff',
        entityPopup : '.snarc-entity-view',
        entityPopupTemplate: "#snarc_entity_view_template",
        toggleEntitiesVisibility: ".toggleEntities",
        entitiesVisibleIcon: "icon-eye",
        entitiesHiddenIcon: "icon-eye-off",
        SNARCActiveArea: '.active_area',
        SNARCAnottationIcon: 'icon-th-list',
        SNARCSocialResultsIcon: 'icon-chart-area',
        entityViewLink : '.entity_view-link',
        entityViewContainer:'.entity_view_linkDisplay',
        socialBarExpandMultimedia : '.snarcStream-expand'

    },
    'CONSTANTS': {
        is_snarc_in_action: false,
        hideEntities:false,
        hide : 'hideSNARC',
        hideHandleBar: 'hideHandlBar'
    },
    'FUNCTIONS' : {
        hideStatusMessage    : function() { $('.snarc-status, .snarc_message_bar .spinner').hide(); },
        hideSNARCLoader      : function() { $('.snarc-sidebar .loading').hide(); },
        hideSNARC            : function() { $('.snarc-sidebar').toggleClass(window.snarc.CONSTANTS.hide); $('.snarc-closeHandler').toggleClass(window.snarc.CONSTANTS.hideHandleBar);},
        removeSNARC          : function() { $('.snarc-sidebar').fadeOut('slow',function(){
                $(this).remove();
                window.snarc.CONSTANTS.is_snarc_in_action = false;
                $('body').removeHighlight();
            });
        },
        changeScreenIconAnottationToSocial     : function() {$('.active_area').removeClass('icon-th-list').addClass('icon-chart-area');},
        changeScreenIconSocialToAnnotation     : function() {$('.active_area').removeClass('icon-chart-area').addClass('icon-th-list');},
        changeScreensAnottationToSocial        : function() {$('#document_annotations').hide(); $('#realted_social').show();},
        changeScreensSocialToAnottation        : function() {$('#realted_social').hide(); $('#document_annotations').show();},
        addModal                               : function() {$(window.snarc.DOM.socialBarExpandMultimedia).magnificPopup({ disableOn: 700, type: 'iframe', mainClass: 'mfp-fade', removalDelay: 160, preloader: true, fixedContentPos: false });},
        attachIntentsHandler                   : function() {
            $('.snarcStream-intents a').click(function(e)
            {
                this.width = 640; this.height = 420;
                e.preventDefault();
                var url = $(this).attr("href");
                var popupName = 'popup_' + this.width + 'x' + this.height;
                var left = (screen.width- this.width)/2;
                var top = ((screen.height- this.height)/2)+25;
                var params = 'width=' + this.width + ',height=' + this.height + ',location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,left=' + left + ',top=' + top;
                window[popupName] = window.open(url, popupName, params);
                if(window.focus) { window[popupName].focus(); }
          });
        }
    },
    'ACTIONS' : {
        initialize : "buildSidebar"
    },
    "VIEWS" : {
        Semanticdocument: {},
        keywords : {}
    },
    'URLS': {
        SNARC_service_url: 'http://ahmadassaf.com/projects/SNARC/SNARC-AJAX.php',
        SNARC_social_url : 'http://ahmadassaf.com/projects/SNARC/SNARC-AJAX-SOCIAL.php'
    },
    'MESSAGES': {
        failedAJAXCall: 'Error Loading Social Bar .. Please Try to Refresh !',
        fetchingSocial: 'Fetching Related Social News ... '
    }
});