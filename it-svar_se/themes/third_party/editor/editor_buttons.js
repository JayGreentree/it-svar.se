if (typeof(EditorButtons) == 'undefined') var EditorButtons = {};
EditorButtons.Link = {};
EditorButtons.PastePlainText = {};
EditorButtons.Templates = {};
EditorButtons.Styles = {};
// ********************************************************************************* //

EditorButtons.Link.OpenModal = function(obj, event, key){
	obj.saveSelection();

	var callback = $.proxy(function()
	{
		this.insert_link_node = false;
		var sel = this.getSelection();
		var url = '', text = '', target = '';

		if (this.browser('msie'))
		{
			var current = this.getCurrentNode();
			var parent = this.getParentNode();
			if (current.nodeName === 'A')
			{
				this.insert_link_node = $(current);
				text = this.insert_link_node.text();
				url = this.insert_link_node.attr('href');
				target = this.insert_link_node.attr('target');
			}
			else if (parent.nodeName === 'A')
			{
				this.insert_link_node = $(parent);
				text = this.insert_link_node.text();
				url = this.insert_link_node.attr('href');
				target = this.insert_link_node.attr('target');
			}
			else
			{
				if (this.oldIE())
				{
					text = sel.text;
				}
				else
				{
					text = sel.toString();
				}
			}
		}
		else
		{
			if (sel && sel.anchorNode && sel.anchorNode.parentNode.tagName === 'A')
			{
				url = sel.anchorNode.parentNode.href;
				text = sel.anchorNode.parentNode.text;
				target = sel.anchorNode.parentNode.target;

				if (sel.toString() === '')
				{
					this.insert_link_node = sel.anchorNode.parentNode;
				}
			}
			else
			{
				text = sel.toString();
			}
		}

		$('.redactor_link_text').val(text);

		var thref = self.location.href.replace(/\/$/i, '');
		var turl = url.replace(thref, '');

		if (url.search('mailto:') === 0)
		{
			obj.setModalTab(4);

			$('#redactor_tab_selected').val(2);
			$('#redactor_link_mailto').val(url.replace('mailto:', ''));
		}
		else if (turl.search(/^#/gi) === 0)
		{
			obj.setModalTab(5);

			$('#redactor_tab_selected').val(3);
			$('#redactor_link_anchor').val(turl.replace(/^#/gi, ''));
		}
		else if ( $('#redactor_navee').find("option[value='"+url+"']").length > 0 )
		{
			obj.setModalTab(3);

			$('#redactor_tab_selected').val(3);
			$('#redactor_navee').val(url);
		}
		else if ( $('#redactor_site_page').find("option[value='"+url+"']").length > 0 )
		{
			obj.setModalTab(2);

			$('#redactor_tab_selected').val(2);
			$('#redactor_site_page').val(url);
		}
		else
		{
			$('#redactor_link_url').val(url);
		}

		if (target === '_blank')
		{
			$('.redactor_link_blank').attr('checked', true);
		}

		$('#redactor_insert_link_btn').click($.proxy(function(e){ EditorButtons.Link.InsertLink(obj, e); }, obj));


	}, obj);

	var endCallback = function(url)
	{
		$('#redactor_link_url').focus();

		$('#redactor_site_page').change(function(){
			if (! $('#redactor_site_page_text').data('filled')) {
				$('#redactor_site_page_text').attr('value', $("#redactor_site_page").children("option").filter(":selected").text() );
			}
		}).trigger('change');
	};

	var ModalContent = EditorButtons.Link.ModalContent();
	obj.modalInit(RLANG.link, ModalContent, 460, callback, endCallback);
};

//********************************************************************************* //

EditorButtons.Link.ModalContent = function(){

	var H = [];

	H.push('<div id="redactor_modal_content">');
	H.push('<form id="redactorInsertLinkForm" method="post" action="">');
		H.push('<div id="redactor_tabs">');
			H.push('<a href="javascript:void(null);" class="redactor_tabs_act">URL</a>');

			if (EditorButtons.site_pages) H.push('<a href="javascript:void(null);">Site Page</a>');
			else {
				EditorButtons.site_pages = '';
				H.push('<a href="javascript:void(null);" style="display:none;">Site Page</a>');
			}

			if (EditorButtons.navee) H.push('<a href="javascript:void(null);">NavEE</a>');
			else {
				EditorButtons.navee = '';
				H.push('<a href="javascript:void(null);" style="display:none;">NavEE</a>');
			}

			H.push('<a href="javascript:void(null);">Email</a>');
			H.push('<a href="javascript:void(null);">' + RLANG.anchor + '</a>');
		H.push('</div>');
			H.push('<input type="hidden" id="redactor_tab_selected" value="1" />');

			H.push('<div class="redactor_tab" id="redactor_tab1">' +
						'<label>URL</label><input type="text" id="redactor_link_url" class="redactor_input"  />' +
						'<label>' + RLANG.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_url_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_wwwink_blank"> ' + RLANG.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab2" style="display: none;">' +
						'<label>Site Page</label><select id="redactor_site_page" style="width:100%">' + EditorButtons.site_pages + '</select>' +
						'<label>' + RLANG.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_site_page_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_site_page_blank"> ' + RLANG.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab3" style="display: none;">' +
						'<label>Link Item</label><select id="redactor_navee" style="width:100%">' + EditorButtons.navee + '</select>' +
						'<label>' + RLANG.text + '</label><input type="text"  class="redactor_input redactor_link_text" id="redactor_site_page_text" />' +
						'<label><input type="checkbox" class="redactor_link_blank" id="redactor_navee_blank"> ' + RLANG.link_new_tab +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab4" style="display: none;">' +
						'<label>Email</label><input type="text" id="redactor_link_mailto" class="redactor_input" />' +
						'<label>' + RLANG.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_mailto_text" />' +
					'</div>');
			H.push('<div class="redactor_tab" id="redactor_tab5" style="display: none;">' +
						'<label>' + RLANG.anchor + '</label><input type="text" class="redactor_input" id="redactor_link_anchor"  />' +
						'<label>' + RLANG.text + '</label><input type="text" class="redactor_input redactor_link_text" id="redactor_link_anchor_text" />' +
					'</div>');

		H.push('</form>');
		H.push('</div>');
		H.push('<div id="redactor_modal_footer">');
			H.push('<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">' + RLANG.cancel + '</a>');
			H.push('<input type="button" class="redactor_modal_btn" id="redactor_insert_link_btn" value="' + RLANG.insert + '" />');
		H.push('</div>');

	return H.join('');
};

//********************************************************************************* //

EditorButtons.Link.InsertLink = function(obj, Event){
	var tab_selected = $('#redactor_tab_selected').val();
	var link = '', text = '', target = '';
	var re;

	if (tab_selected === '1') // url
	{
		link = $('#redactor_link_url').val();
		text = $('#redactor_link_url_text').val();

		if ($('#redactor_wwwink_blank').attr('checked'))
		{
			target = '_blank';
		}

		// test http
		re = new RegExp('^https?://', 'i');
		if (link.search(re) == -1)
		{
			link = obj.opts.protocol + link;
		}
	}
	else if (tab_selected === '2') // site pages
	{
		link = $('#redactor_site_page').val();
		text = $('#redactor_site_page_text').val();

		if ($('#redactor_site_page_blank').attr('checked'))
		{
			target = '_blank';
		}

		// test http
		re = new RegExp('^https?://', 'i');
		if (link.search(re) == -1)
		{
			link = obj.opts.protocol + link;
		}
	}
	else if (tab_selected === '4') // mailto
	{
		link = 'mailto:' + $('#redactor_link_mailto').val();
		text = $('#redactor_link_mailto_text').val();
	}
	else if (tab_selected === '5') // anchor
	{
		link = '#' + $('#redactor_link_anchor').val();
		text = $('#redactor_link_anchor_text').val();
	}

	obj._insertLink('<a href="' + link + '" target="' + target + '">' +  text + '</a>', $.trim(text), link, target);
};

//********************************************************************************* //


EditorButtons.PastePlainText.OpenModal = function(obj, event, key){
	obj.saveSelection();

	var handler = $.proxy(function(){

		$('#redactor_paste_plaintext_area').bind('paste', $.proxy(function(e){ EditorButtons.PastePlainText.ProcessPaste(obj, e); }, obj));
		$('#redactor_save_plain_text').click($.proxy(function(e){ EditorButtons.PastePlainText.SavePaste(obj, e); }, obj));

	}, obj);

	var endCallback = function(url){

	};

	var ModalContent = '<div id="redactor_modal_content"><textarea id="redactor_paste_plaintext_area" style="height:400px"></textarea>' +
				'<div id="redactor_modal_footer">' +
					'<span class="redactor_btns_box">' +
						'<a href="javascript:void(null);" class="redactor_modal_btn redactor_btn_modal_close">' + RLANG.cancel + '</a>' +
						'<input type="button" class="redactor_modal_btn" id="redactor_save_plain_text" value="' + RLANG.insert + '" />' +
					'</span>' +
				'</div></div>';

	obj.modalInit('Paste Plain Text', ModalContent, 600, handler, endCallback);
};

//********************************************************************************* //

EditorButtons.PastePlainText.ProcessPaste = function(obj, e){
	var el = $(e.target);

    setTimeout(function() {

    	var content = $(el).val();
		var textCharsRegExp = /[<>&\u007E-\uD7FF\uE000-\uFFEF]|[\uD800-\uDBFF][\uDC00-\uDFFF]/g;
		var baseEntities = {
			'\"' : '&quot;', // Needs to be escaped since the YUI compressor would otherwise break the code
			"'" : '&#39;',
			'<' : '&lt;',
			'>' : '&gt;',
			'&' : '&amp;'
		};
		var reverseEntities = {
			'&lt;' : '<',
			'&gt;' : '>',
			'&amp;' : '&',
			'&quot;' : '"',
			'&apos;' : "'"
		};
		var entityRegExp = /&(#x|#)?([\w]+);/g;

		var asciiMap = {
				128 : "\u20AC", 130 : "\u201A", 131 : "\u0192", 132 : "\u201E", 133 : "\u2026", 134 : "\u2020",
				135 : "\u2021", 136 : "\u02C6", 137 : "\u2030", 138 : "\u0160", 139 : "\u2039", 140 : "\u0152",
				142 : "\u017D", 145 : "\u2018", 146 : "\u2019", 147 : "\u201C", 148 : "\u201D", 149 : "\u2022",
				150 : "\u2013", 151 : "\u2014", 152 : "\u02DC", 153 : "\u2122", 154 : "\u0161", 155 : "\u203A",
				156 : "\u0153", 158 : "\u017E", 159 : "\u0178"
		};

		// Build a two way lookup table for the entities
		function buildEntitiesLookup(items, radix) {
			var i, chr, entity, lookup = {};

			if (items) {
				items = items.split(',');
				radix = radix || 10;

				// Build entities lookup table
				for (i = 0; i < items.length; i += 2) {
					chr = String.fromCharCode(parseInt(items[i], radix));

					// Only add non base entities
					if (!baseEntities[chr]) {
						entity = '&' + items[i + 1] + ';';
						lookup[chr] = entity;
						lookup[entity] = chr;
					}
				}

				return lookup;
			}
		}

		var namedEntities = buildEntitiesLookup(
		'50,nbsp,51,iexcl,52,cent,53,pound,54,curren,55,yen,56,brvbar,57,sect,58,uml,59,copy,' +
		'5a,ordf,5b,laquo,5c,not,5d,shy,5e,reg,5f,macr,5g,deg,5h,plusmn,5i,sup2,5j,sup3,5k,acute,' +
		'5l,micro,5m,para,5n,middot,5o,cedil,5p,sup1,5q,ordm,5r,raquo,5s,frac14,5t,frac12,5u,frac34,' +
		'5v,iquest,60,Agrave,61,Aacute,62,Acirc,63,Atilde,64,Auml,65,Aring,66,AElig,67,Ccedil,' +
		'68,Egrave,69,Eacute,6a,Ecirc,6b,Euml,6c,Igrave,6d,Iacute,6e,Icirc,6f,Iuml,6g,ETH,6h,Ntilde,' +
		'6i,Ograve,6j,Oacute,6k,Ocirc,6l,Otilde,6m,Ouml,6n,times,6o,Oslash,6p,Ugrave,6q,Uacute,' +
		'6r,Ucirc,6s,Uuml,6t,Yacute,6u,THORN,6v,szlig,70,agrave,71,aacute,72,acirc,73,atilde,74,auml,' +
		'75,aring,76,aelig,77,ccedil,78,egrave,79,eacute,7a,ecirc,7b,euml,7c,igrave,7d,iacute,7e,icirc,' +
		'7f,iuml,7g,eth,7h,ntilde,7i,ograve,7j,oacute,7k,ocirc,7l,otilde,7m,ouml,7n,divide,7o,oslash,' +
		'7p,ugrave,7q,uacute,7r,ucirc,7s,uuml,7t,yacute,7u,thorn,7v,yuml,ci,fnof,sh,Alpha,si,Beta,' +
		'sj,Gamma,sk,Delta,sl,Epsilon,sm,Zeta,sn,Eta,so,Theta,sp,Iota,sq,Kappa,sr,Lambda,ss,Mu,' +
		'st,Nu,su,Xi,sv,Omicron,t0,Pi,t1,Rho,t3,Sigma,t4,Tau,t5,Upsilon,t6,Phi,t7,Chi,t8,Psi,' +
		't9,Omega,th,alpha,ti,beta,tj,gamma,tk,delta,tl,epsilon,tm,zeta,tn,eta,to,theta,tp,iota,' +
		'tq,kappa,tr,lambda,ts,mu,tt,nu,tu,xi,tv,omicron,u0,pi,u1,rho,u2,sigmaf,u3,sigma,u4,tau,' +
		'u5,upsilon,u6,phi,u7,chi,u8,psi,u9,omega,uh,thetasym,ui,upsih,um,piv,812,bull,816,hellip,' +
		'81i,prime,81j,Prime,81u,oline,824,frasl,88o,weierp,88h,image,88s,real,892,trade,89l,alefsym,' +
		'8cg,larr,8ch,uarr,8ci,rarr,8cj,darr,8ck,harr,8dl,crarr,8eg,lArr,8eh,uArr,8ei,rArr,8ej,dArr,' +
		'8ek,hArr,8g0,forall,8g2,part,8g3,exist,8g5,empty,8g7,nabla,8g8,isin,8g9,notin,8gb,ni,8gf,prod,' +
		'8gh,sum,8gi,minus,8gn,lowast,8gq,radic,8gt,prop,8gu,infin,8h0,ang,8h7,and,8h8,or,8h9,cap,8ha,cup,' +
		'8hb,int,8hk,there4,8hs,sim,8i5,cong,8i8,asymp,8j0,ne,8j1,equiv,8j4,le,8j5,ge,8k2,sub,8k3,sup,8k4,' +
		'nsub,8k6,sube,8k7,supe,8kl,oplus,8kn,otimes,8l5,perp,8m5,sdot,8o8,lceil,8o9,rceil,8oa,lfloor,8ob,' +
		'rfloor,8p9,lang,8pa,rang,9ea,loz,9j0,spades,9j3,clubs,9j5,hearts,9j6,diams,ai,OElig,aj,oelig,b0,' +
		'Scaron,b1,scaron,bo,Yuml,m6,circ,ms,tilde,802,ensp,803,emsp,809,thinsp,80c,zwnj,80d,zwj,80e,lrm,' +
		'80f,rlm,80j,ndash,80k,mdash,80o,lsquo,80p,rsquo,80q,sbquo,80s,ldquo,80t,rdquo,80u,bdquo,810,dagger,' +
		'811,Dagger,81g,permil,81p,lsaquo,81q,rsaquo,85c,euro', 32);

		function process(items) {
			$.each(items, function(i, v) {
				// Remove or replace
				if (v.constructor == RegExp)
					content = content.replace(v, '');
				else
					content = content.replace(v[0], v[1]);
			});
		}

		function nativeDecode(text) {
			var elm;

			elm = document.createElement("div");
			elm.innerHTML = text;

			return elm.textContent || elm.innerText || text;
		}

		function decode (text) {
			return text.replace(entityRegExp, function(all, numeric, value) {
				if (numeric) {
					value = parseInt(value, numeric.length === 2 ? 16 : 10);

					// Support upper UTF
					if (value > 0xFFFF) {
						value -= 0x10000;

						return String.fromCharCode(0xD800 + (value >> 10), 0xDC00 + (value & 0x3FF));
					} else
						return asciiMap[value] || String.fromCharCode(value);
				}

				return reverseEntities[all] || namedEntities[all] || nativeDecode(all);
			});
		}

		// remove comments and php tags
		content = content.replace(/<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi, '');

		// remove attributes
		content = content.replace(/<(\w+)([\w\W]*?)>/gi, '<$1>');

   	 	// If HTML content with line-breaking tags, then remove all cr/lf chars because only tags will break a line
		if (/<(?:p|br|h[1-6]|ul|ol|dl|table|t[rdh]|div|blockquote|fieldset|pre|address|center)[^>]*>/i.test(content)) {
			process([
				/[\n\r]+/g
			]);
		} else {
			// Otherwise just get rid of carriage returns (only need linefeeds)
			process([
				/\r+/g
			]);
		}

		process([
			[/<\/(?:p|h[1-6]|ul|ol|dl|table|div|blockquote|fieldset|pre|address|center)>/gi, "\n\n"],		// Block tags get a blank line after them
			[/<br[^>]*>|<\/tr>/gi, "\n"],				// Single linebreak for <br /> tags and table rows
			[/<\/t[dh]>\s*<t[dh][^>]*>/gi, "\t"],		// Table cells get tabs betweem them
			/<[a-z!\/?][^>]*>/gi,						// Delete all remaining tags
			[/&nbsp;/gi, " "],							// Convert non-break spaces to regular spaces (remember, *plain text*)
			[/(?:(?!\n)\s)*(\n+)(?:(?!\n)\s)*/gi, "$1"] // Cool little RegExp deletes whitespace around linebreak chars.
		]);

		var maxLinebreaks = 2;
		if (maxLinebreaks > -1) {
			var maxLinebreaksRegex = new RegExp("\n{" + (maxLinebreaks + 1) + ",}", "g");
			var linebreakReplacement = "";

			while (linebreakReplacement.length < maxLinebreaks) {
				linebreakReplacement += "\n";
			}

			process([
				[maxLinebreaksRegex, linebreakReplacement] // Limit max consecutive linebreaks
			]);
		}

		// Encode
		content.replace(textCharsRegExp, function(chr) {
			return baseEntities[chr] || chr;
		});

		// Decode it
		content = decode(content);

		// Convert single line breaks to <br /> and double line breaks to <p>...</p>
		process([
			[/\n\n/g, "</p><p>"],
			[/^(.*<\/p>)(<p>)$/, '<p>$1'],
			[/\n/g, "<br />"]
		]);

		// Remove bad attributes, with or without quotes, ensuring that attribute text is really inside a tag.
		// If JavaScript had a RegExp look-behind, we could have integrated this with the last process() array and got rid of the loop. But alas, it does not, so we cannot.
		do {
			len = content.length;
			content = content.replace(/(<[a-z][^>]*\s)(?:id|name|language|type|on\w+|\w+:\w+)=(?:"[^"]*"|\w+)\s?/gi, "$1");
		} while (len != content.length);

		// remove empty
		content = content.replace(/<[^\/>][^>]*>(\s*|\t*|\n*|&nbsp;|<br>)<\/[^>]+>/gi, '');
		content = content.replace(/<[^\/>][^>]*>(\s*|\t*|\n*|&nbsp;|<br>)<\/[^>]+>/gi, '');

		// remove span
		content = content.replace(/<span>([\w\W]*?)<\/span>/gi, '$1');
		content = content.replace(/\n{3,}/gi, '\n');

		// remove dirty p
		content = content.replace(/<p><p>/g, '<p>');
		content = content.replace(/<\/p><\/p>/g, '</p>');

		$(el).html(content);

    }, 100);

};

//********************************************************************************* //

EditorButtons.PastePlainText.SavePaste = function(obj, e){
	var content = $('#redactor_paste_plaintext_area').html();

	// remove dirty p
	content = content.replace(/<p><p>/g, '<p>');
	content = content.replace(/<\/p><\/p>/g, '</p>');
	content = content.replace(/&lt;/g, '<');
	content = content.replace(/&gt;/g, '>');

	obj.insertHtml(content);
	obj.syncCode();
	obj.modalClose();

	Editor.TriggerRedactorCleanup(obj);
};

//********************************************************************************* //

EditorButtons.NoSettingsAlert = function(redactor, e, btnkey){
    alert('No button settings have been defined');
};

// ********************************************************************************* //

EditorButtons.Templates.InsertTemplate = function(redactor, event, key){
    if (typeof(Editor.settings) == 'undefined') return;
    if (typeof(Editor.settings.templates) == 'undefined') return;
    if (typeof(Editor.settings.templates.templates) == 'undefined') return;
    if (typeof(Editor.settings.templates.templates[key]) == 'undefined') return;

    var html = Editor.settings.templates.templates[key].html;
    redactor.insertHtml(html);
};

// ********************************************************************************* //


EditorButtons.Styles.OpenDropDown = function(redactor, e, btnkey){
    if (typeof(Editor.settings) == 'undefined') return;
    if (typeof(Editor.settings.styles) == 'undefined') return;
    if (typeof(Editor.settings.styles.styles) == 'undefined') return;

    var dropdown = $('#EditorButtonsStyles');
    var item;

    if (dropdown.length == 0) {
        var Styles = Editor.settings.styles.styles;

        dropdown = $('<div id="EditorButtonsStyles" class="redactor_dropdown" style="display:none;"></div>');

        for (var i = 0; i < Styles.length; i++) {
            item = $('<a class="" style="' + Styles[i].btn_style + '" class="'+Styles[i].btn_class+'" href="javascript:void(null);" data-key="'+i+'"><span class="ind"></span>'+Styles[i].title+'</a>');
            item.click($.proxy(function(e) { EditorButtons.Styles.TriggerItem(redactor, e); }, redactor));
            dropdown.append(item);
        };

        $('body').append(dropdown);
    }

    redactor.hideAllDropDown();
    redactor.setBtnActive(btnkey);
    redactor.getBtn(btnkey).addClass('dropact');

    var left = redactor.getBtn(btnkey).offset().left;

    if (redactor.opts.air)
    {
        var air_top = redactor.air.offset().top;

        dropdown.css({ position: 'absolute', left: left + 'px', top: air_top+30 + 'px' }).show();
    }
    else if (redactor.opts.fixed && redactor.fixed)
    {
        dropdown.css({ position: 'fixed', left: left + 'px', top: '30px' }).show();
    }
    else
    {
        var top = redactor.$toolbar.offset().top + 30;
        dropdown.css({ position: 'absolute', left: left + 'px', top: top + 'px' }).show();
    }

    var hdlHideDropDown = $.proxy(function(e) { redactor.hideDropDown(e, dropdown, key); }, redactor);

    $(document).one('click', hdlHideDropDown);
    redactor.$editor.one('click', hdlHideDropDown);

    e.stopPropagation();
};

// ********************************************************************************* //

EditorButtons.Styles.TriggerItem = function(redactor, e){
    // Save the current state
    redactor.setBuffer();

    if (redactor.getCurrentNode().className == 'redactor_box') {
        return;
    }

    if (redactor.getCurrentNode().tagName.toLowerCase() == 'body') {
        return;
    }

    var key = e.target.getAttribute('data-key');
    var tmpl = Editor.settings.styles.styles[key];
    var elem, temp;

    // Lets parse the attributes
    var attributes = {};
    if (tmpl.attr){
        temp = $('<div '+tmpl.attr+'>');

        for (var i = temp[0].attributes.length - 1; i >= 0; i--) {
            attributes[temp[0].attributes[i].name] = temp[0].attributes[i].value;
        };

    }

    tmpl.attr_obj = attributes;

    var selection = redactor.getSelectedHtml();
    var selection_obj = $(selection);

    // If empty string
    if (!selection) {
        tmpl.type = 'current';
    }

    if (selection_obj.length > 0) {
        alert('Applying styles to multiple paragraph is not supported');
        return;
    }

    if (tmpl.type == 'inline') {
        EditorButtons.Styles.Exec_Block_Inline('inline', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'block') {
        EditorButtons.Styles.Exec_Block_Inline('block', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'custom') {
        EditorButtons.Styles.Exec_Block_Inline('custom', redactor, tmpl, selection);
    }

    else if (tmpl.type == 'current') {
        elem = $(redactor.getCurrentNode());
        elem.attr('style', tmpl.styles);
        elem.attr(tmpl.attr_obj);
        elem.addClass(tmpl.classes);
        Editor.TriggerRedactorCleanup(redactor);
    }
};

// ********************************************************************************* //

EditorButtons.Styles.Exec_Block_Inline = function(type, redactor, tmpl, selection){
    var elem = $(redactor.getCurrentNode());
    var temp;

    if (type == 'inline') {
    	temp = $('<div><span></span></div>').find('span');
    } else if (type == 'block') {
    	temp = $('<div><div></div></div>').find('div');
    } else if (type == 'custom') {
    	temp = $('<div><'+tmpl.custom_type+'></'+tmpl.custom_type+'></div>').find(tmpl.custom_type);
    } else {
    	temp = $('<div><span></span></div>').find('span');
    }

    var temp = temp.attr('style', tmpl.styles).attr('class', tmpl.classes).attr(tmpl.attr_obj).html(selection).parent().html();
    elem.html(elem.html().replace(selection, temp) );
    Editor.TriggerRedactorCleanup(redactor);
};

// ********************************************************************************* //
