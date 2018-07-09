<script id="snarc_entity_view_template" type="text/x-jquery-tmpl">
<div class="entity_view-Name">${entity}</div>
{{if type.length > 0}}
<div class="entity_view-types">
	{{if type instanceof Array }}
		{{each type}} <span class="entityType">${$value}</span> {{/each}}
	{{else}} <span class="entityType">${type}</span>{{/if}}
{{/if}}
</div>
{{if links.length > 0}}
	<div class="entity_view-links">
		{{each links}}
			<a data-type="${$value.type}" href="${$value.url}" class="entity_view-link" data-link="${$value.url}">
				{{if $value.type == 'wikipedia'}} <i class="icon-wikipedia"></i>
				{{else $value.type == 'homepage'}}<i class="icon-home"></i>
				{{else $value.type == 'geolocation'}}<i class="icon-location"></i>
				{{else}}<i class="icon-globe-1"></i>
				{{/if}}
			${$value.type}</a>
		{{/each}}
	</div>
{{/if}}
	{{each links}}
			{{if $value.type == 'wikipedia'}} 
				{{if description}}
					<span class="entity_view_description">{{html String($value.description).trim().replace(/[()]/g,'') }}</span>
				{{/if}}
			{{/if}}
	{{/each}}
</script>