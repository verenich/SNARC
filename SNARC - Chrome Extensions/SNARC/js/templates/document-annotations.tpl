<div class="snarc_main_information">
	<div class="snarc_document_title">${title}</div>
	<div class="snarcTopMeta">
		<div class="snarc_property">Language: ${language}</div>
		<div class="snarc_property red">${categories.alchemy}</div>
		{{each categories.zemanta}}
			<div class="snarc_property red">${$value}</div>
		{{/each}}	
	</div>
</div>
<div class="snarc_annotation">
	<div class="snarc_section_title"><i class="icon-tag"></i>Extracted Keywords</div>
	<div class="snarc_list">
		{{each keywords}} <div class="snarc_list_row"> ${text} <span class="additionalMeta green">${String(relevance * 100).substring(0, 4)}%</span> </div> {{/each}}		
	</div>
</div>
<div class="snarc_annotation">
	<div class="snarc_section_title"><i class="icon-qrcode"></i>Extracted Entities</div>
	<div class="snarc_list">
		{{each entities}}
		<div class="snarc_list_row" data-entity="${entity}" data-type="${type}" data-relevance="${relevance}">${entity}
			{{if type.length > 0}}
			<span class="additionalMeta">
				{{if type instanceof Array }}
					{{each type}} <span class="entityType yellow">${$value}</span> {{/each}}
				{{else}} <span class="entityType yellow">${type}</span>{{/if}}
			</span>	
			{{/if}}		
		</div>
		{{/each}}	
	</div>
</div>
