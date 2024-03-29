/*

highlight v4

Highlights arbitrary terms.

<http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html>

MIT license.

Johann Burkard
<http://johannburkard.de>
<mailto:jb@eaio.com>

*/

jQuery.fn.highlight = function(pat, entity)
{
    function innerHighlight(node, pat, entity)
    {
        var skip = 0;       
        if (node.nodeType == 3)
        {
            var pos = node.data.toUpperCase().indexOf(pat);
            if (pos >= 0)
            {
                var spannode = document.createElement('span');
                spannode.className = 'SNARCHighlight';
                spannode.setAttribute("data-entity", entity);
                var middlebit = node.splitText(pos);
                var endbit = middlebit.splitText(pat.length);
                var middleclone = middlebit.cloneNode(true);
                spannode.appendChild(middleclone);
                middlebit.parentNode.replaceChild(spannode, middlebit);
                skip = 1;
            }
        }
        else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName))
        {
            for (var i = 0; i < node.childNodes.length; ++i)
            {
                i += innerHighlight(node.childNodes[i], pat,entity);
            }
        }
        return skip;
    }

    return this.length && pat && pat.length ? this.each(function()
    {
        innerHighlight(this, pat.toUpperCase(), entity);
    }) : this;
};

jQuery.fn.removeHighlight = function()
{
    return this.find("span.SNARCHighlight").each(function()
    {
        this.parentNode.firstChild.nodeName;
        with(this.parentNode)
        {
            replaceChild(this.firstChild, this);
            normalize();
        }
    }).end();
};