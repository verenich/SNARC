{{if type === "micropost" && $data.title }}
	<li class="snarcStream-micropost{{if service == 'twitter'}} snarcStream-tweet {{/if}}" id="${link}">
		{{if thumbnail}}<img width="60" height="80" class="snarcStream-photo" src="${thumbnail}">{{/if}}				
		<div class="snarcStream-text">
		<a class="snarcStream-profile" target="_blank" {{if service == 'twitter'}}href="http://www.twitter.com/${author}"
			{{else}} href="${profile}" {{/if}}>@${author}</a>
			{{if service == 'twitter'}} {{html title}} {{else}} ${title} {{/if}}
			{{if service == 'twitter'}}<span>${time}</span>{{/if}}
		</div>
		<div class="snarcStream-infoBar">
			<span class="snarcStream-info">
				{{if service == 'twitter'}} <i class="icon-twitter-bird"></i>{{if program }}&nbsp;via {{html program}} {{/if}} {{/if}}
				{{if service == 'google'}} 
					<i class="icon-googleplus-rect"></i>&nbsp;${time} 
				{{/if}}
			</span>
			{{if service == 'google'}}
				<span class="snarcStream-expand"><a class="icon-dot-3" target="_blank" href="${link}"></a></span>
			{{/if}}
			{{if service == 'twitter'}}
				<div class="snarcStream-intents">
					<a href="https://twitter.com/intent/tweet?in_reply_to=${link}"><i class="icon-reply"></i>Reply</a>
					<a href="https://twitter.com/intent/retweet?tweet_id=${link}"><i class="icon-retweet"></i>Retweet</a>
					<a href="https://twitter.com/intent/favorite?tweet_id=${link}"><i class="icon-star"></i>Favorite</a>
				</div>
			{{/if}}	
		</div>
	</li>
{{else type === "question"}}
	<li class="snarcStream-question">
		<span class="snarcStream-type green">Question</span>
		<a class="snarcStream-title" href="http://stackoverflow.com/questions/${link}">${title}</a>
		<div class="snarcStream-bottom_bar">
			<span class="snarcStream-info"><i class="icon-stackoverflow"></i>&nbsp;${time}</span>
		</div>
	</li>
{{else type === "post"}}
	<li class="snarcStream-post">
		<a class="snarcStream-title" href="${link}">${title}</a>
		{{if $data.content !== undefined }}
			<div class="snarcStream-extra_content">{{html String($data.content).trim().replace('/[^\w\s]/gi', '')}}</div>
		{{/if}}
		<div class="snarcStream-bottom_bar">
			<span class="snarcStream-info"><i class="icon-doc"></i>&nbsp;${time}</span>
			{{if share.total !== 0 }}<span class="snarc-post_share"><i class="icon-share"></i>${share.total}</span>{{/if}}
		</div>
	</li>
{{else type === "slide"}}
	<li class="snarcStream-slide">
		<span class="snarcStream-type yellow">Presentation</span>
		<div class="snarcStream-title">${title}</div>
		{{if description}}
		<div class="snarcStream-extra_content">{{html String($data.description).trim().replace('/[^\w\s]/gi', '')}}</div>
		{{/if}}
		<div class="snarcStream-bottom_bar">
			<span class="snarcStream-info"><i class="icon-slideshare"></i>&nbsp;${time}</span>
			<a href="${link}" class="snarcStream-expand"><i class="icon-dot-3"></i></a>
		</div>
	</li>
{{else type === "video"}}
	<li class="snarcStream-video"  data-source = "${service}">
		<span class="snarcStream-type{{if service == 'youtube'}} red {{else}} blue {{/if}}">Video</span>
		<div class="snarcStream-title"> ${title} </div>
		<div class="snarcStream-infoBar">
			{{if service == 'youtube'}} 
				<div href="${link}" class ="snarcVideoThumbnail snarcStream-expand" style="background: url('${thumbnail}') center center no-repeat;"></div>
			{{/if}}
			<span class="snarcStream-info">
				{{if service == 'youtube'}}  <i class="icon-youtube"></i> 
				{{else}} <i class="icon-vimeo-rect"></i>
				{{/if}} &nbsp;${time}
			</span>
			{{if service == 'vimeo'}}
				<a href="${link}" class="snarcStream-expand"><i class="icon-dot-3"></i></a>
			{{/if}}
		</div>
	</li>
{{/if}}