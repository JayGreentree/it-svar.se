// ********************************************************************************* //
if (typeof(Forms) == 'undefined') var Forms = {};
Forms.Conditionals = {};
// ********************************************************************************* //

jQuery(document).ready(function(){

	var FormField, Input;

	// Find all Forms
	Forms.dform = $('div.dform');

	// Did are there any conditionals stored?
	for (var Field in Forms.Conditionals){
		var FIELD_ID = Field.substr(3);
		// Does is Exists?
		if (document.getElementById('forms_field_'+FIELD_ID) === null) continue;

		for (var i = 0; i < Forms.Conditionals[Field].conditionals.length; i++) {
			var Cond = Forms.Conditionals[Field].conditionals[i];
			FormField = $('#forms_field_'+Cond.field);
			Input = FormField.find(':input[name^="fields\\['+Cond.field+'\\]"]'); // Matches field names with array
			//if (Input.data('form_event_added') == true) continue;
			if (Input.length === 0) {
				// Maybe paginated?
				Input = Forms.dform.closest('form').find(':input[name^="fields\\['+Cond.field+'\\]"]');
				if (Input.length === 0) continue;
				else {
					FormField = Input.parent();
				}
			}

			Input.data('form_event_added', true);
			if (Input[0].nodeName == 'SELECT') Input.bind('change.forms_'+Cond.field, {'condkey':i, 'formfield':FormField, 'senderfield':Field},Forms.ExecuteConditional).trigger('change');
			else if (Input[0].type == 'radio') Input.bind('click.forms_'+Cond.field, {'condkey':i, 'formfield':FormField, 'senderfield':Field},Forms.ExecuteConditional).filter(':checked').trigger('click');
			else Input.bind('keyup.forms_'+Cond.field, {'condkey':i, 'formfield':FormField, 'senderfield':Field},Forms.ExecuteConditional).trigger('keyup');

		}
	}

	if (Forms.dform.find('span.forms_cart_total').length > 0) {
		Forms.dform.find('div.dform_cart_product').bind('click keyup change', Forms.CalculateCartTotal);
		Forms.dform.find('div.dform_cart_quantity').bind('click keyup change', Forms.CalculateCartTotal);
		Forms.dform.find('div.dform_cart_product_options').bind('click keyup change', Forms.CalculateCartTotal);
		Forms.dform.find('div.dform_cart_shipping').bind('click keyup change', Forms.CalculateCartTotal);
		Forms.CalculateCartTotal();
	}

});

//********************************************************************************* //

Forms.AddConditional = function(FieldID, Cond){
	Forms.Conditionals['ID_'+FieldID] = Cond;
};

//********************************************************************************* //

Forms.ExecuteConditional = function(Event){
	if (typeof(Event.data) == 'undefined') return;
	if (typeof(Event.data.condkey) == 'undefined') return;
	if (typeof(Forms.Conditionals[Event.data.senderfield].conditionals[Event.data.condkey]) == 'undefined') return;
	var Cond = Forms.Conditionals[Event.data.senderfield].conditionals[Event.data.condkey];
	var Value = '';
	var Passed = false;

	// Is it a radio button?
	if (Event.target.type == 'radio' && Event.target.nodeName == 'INPUT') Value = Event.data.formfield.find(':input[name^="fields\\['+Cond.field+'\\]"]').filter(':checked').val();
	else Value = Event.data.formfield.find(':input[name^="fields\\['+Cond.field+'\\]"]').val();
	if (Value) Value = $.trim(Value);

	switch(Cond.operator) {
		case 'is':
			if (Value == Cond.value) Passed = true;
			break;
		case 'isnot':
			if (Value != Cond.value) Passed = true;
			break;
		case 'greater_then':
			if (parseFloat(Value) > parseFloat(Cond.value)) Passed = true;
			break;
		case 'less_then':
			if (parseFloat(Value) < parseFloat(Cond.value)) Passed = true;
			break;
		case 'contains':
			if ( Value.toLowerCase().indexOf(Cond.value.toLowerCase()) != -1) Passed = true;
			break;
		case 'starts_with':
			if ( Value.toLowerCase().lastIndexOf(Cond.value.toLowerCase(), 0) === 0) Passed = true;
			break;
		case 'ends_with':
			if ( Value.toLowerCase().slice(-Cond.value.toLowerCase().length) == Cond.value.toLowerCase() ) Passed = true;
			break;
	}

	Cond.passed = Passed;
	Forms.FinalizeConditional(Event.data.senderfield);
};

//********************************************************************************* //

Forms.FinalizeConditional = function(FIELD){
	var FIELD_ID = FIELD.substr(3);
	var Field = $('#forms_field_'+FIELD_ID);
	var Cond = Forms.Conditionals[FIELD];

	if (Cond.options.match == 'all'){
		var PassedAll = true;
		for (var i = Cond.conditionals.length - 1; i >= 0; i--) {
			if (typeof(Cond.conditionals[i].passed) == 'undefined') PassedAll = false;
			if (Cond.conditionals[i].passed == false) PassedAll = false;
		}

		if (PassedAll){
			if (Cond.options.display == 'show') Field.show();
			if (Cond.options.display == 'hide') Field.hide();
		} else {
			if (Cond.options.display == 'show') Field.hide();
			if (Cond.options.display == 'hide') Field.show();
		}
	} else	{
		var PassedAny = false;

		for (var i = Cond.conditionals.length - 1; i >= 0; i--) {
			if (typeof(Cond.conditionals[i].passed) == 'undefined') PassedAny = false;
			if (Cond.conditionals[i].passed == true) PassedAny = true;
		}

		if (PassedAny == true){
			if (Cond.options.display == 'show') Field.show();
			if (Cond.options.display == 'hide') Field.hide();
		} else {
			if (Cond.options.display == 'show') Field.hide();
			if (Cond.options.display == 'hide') Field.show();
		}
	}

	Forms.CalculateCartTotal();
};

//********************************************************************************* //

Forms.CalculateCartTotal = function(e){

	Forms.form_elements = Forms.dform.find('div.dform_element:visible');

	Forms.Products = [];
	Forms.Products_Shipping = 0;

	// Loop over all fields
	Forms.form_elements.each(function(i, elem){
		elem = $(elem);

		// Product?
		if (elem.hasClass('dform_cart_product')) {
			Forms.ParseCart_Products(elem); return;
		}

		// Product Options
		if (elem.hasClass('dform_cart_product_options')) {
			Forms.ParseCart_Options(elem); return;
		}

		// Quantity?
		if (elem.hasClass('dform_cart_quantity')) {
			Forms.ParseCart_Quantity(elem); return;
		}

		// Shipping
		if (elem.hasClass('dform_cart_shipping')) {
			Forms.ParseCart_Shipping(elem); return;
		}

	});

	var TotalCart = 0;

	for (var i = Forms.Products.length - 1; i >= 0; i--) {
		var OptionsTotal = 0, ProductTotal = 0, LineTotal = 0;

		if (!Forms.Products[i].price) continue;

		if (Forms.Products[i].option) {
			for (var ii = Forms.Products[i].option.length - 1; ii >= 0; ii--) {
				OptionsTotal += parseFloat(Forms.Products[i].option[ii]);
			}
		}

		ProductTotal = (parseFloat(OptionsTotal) + parseFloat(Forms.Products[i].price));

		if (Forms.Products[i].qty) LineTotal += (parseFloat(Forms.Products[i].qty) * parseFloat(ProductTotal));
		else LineTotal += parseFloat(ProductTotal);

		TotalCart += parseFloat(LineTotal);
	}

	TotalCart += parseFloat(Forms.Products_Shipping);

	Forms.dform.find('span.forms_cart_total').html(parseFloat(TotalCart).toFixed(2));
};

//********************************************************************************* //

Forms.ParseCart_Products = function(elem){
	var Product = {};
	var Selected;

	// Single Product?
	if (elem.find('input.single_product').val() == 'single' || elem.find('input.single_product').val() == 'entry_product' ) {
		Product.price = elem.find('input.single_product').attr('data-price');
		if (elem.find('input.cart_quantity').length > 0) Product.qty = elem.find('input.cart_quantity').val();
		Forms.Products.push(Product);
		return;
	}

	// Radio?
	if (elem.find('ul.radios').length > 0) {
		Selected = elem.find('input[type=radio]:checked');
		if (Selected.length === 0) return;
		Product.price = Selected.parent().attr('data-price');
		if (elem.find('input.cart_quantity').length > 0) Product.qty = elem.find('input.cart_quantity').val();
		Forms.Products.push(Product);
		return;
	}

	// Checkbox
	if (elem.find('ul.checkboxes').length > 0) {
		Selected = elem.find('input[type=checkbox]:checked');
		Selected.each(function(i, el){
			el = $(el).parent();
			Product = {};
			Product.price = el.attr('data-price');
			if (el.find('input.cart_quantity').length > 0) Product.qty = el.find('input.cart_quantity').val();
			Forms.Products.push(Product);
		});
		return;
	}

	// Dropdown
	if (elem.find('select').length > 0) {
		Selected = elem.find('select option:selected');
		if (Selected.length === 0) return;
		Product.price = Selected.attr('data-price');
		Forms.Products.push(Product);
		return;
	}

	// User Defined
	if (elem.find('input.user_defined').length > 0) {
		Product.price = elem.find('input.user_defined').val();
		Forms.Products.push(Product);
		return;
	}
};

//********************************************************************************* //

Forms.ParseCart_Quantity = function(elem){
	var Qty = elem.find('input,select').val();

	for (var i = 0; i < Forms.Products.length; i++) {
		if (!Forms.Products[i].qty) Forms.Products[i].qty = Qty;
	}
};

//********************************************************************************* //

Forms.ParseCart_Options = function(elem){

	var Options = [];
	var Selected;

	// Radio?
	if (elem.find('ul.radios').length > 0) {
		Selected = elem.find('input[type=radio]:checked');
		if (Selected.length === 0) return;
		Options.push(Selected.parent().attr('data-price'));
	}

	// Checkbox
	else if (elem.find('ul.checkboxes').length > 0) {
		Selected = elem.find('input[type=checkbox]:checked');
		Selected.each(function(i, el){
			el = $(el).parent();
			Options.push(el.attr('data-price'));
		});
	}

	// Dropdown
	else if (elem.find('select').length > 0) {
		Selected = elem.find('select option:selected');
		if (Selected.length === 0) return;
		Options.push(Selected.attr('data-price'));
	}

	if (Options.length === 0) return;

	for (var i = 0; i < Forms.Products.length; i++) {
		if (!Forms.Products[i].option) Forms.Products[i].option = Options;
	}
};

//********************************************************************************* //

Forms.ParseCart_Shipping = function(elem) {
	var Selected;

	// Single Ship?
	if (elem.find('input.single_ship').val() == 'single') {
		Forms.Products_Shipping = elem.find('input.single_ship').attr('data-price');
		return;
	}

	// Radio?
	if (elem.find('ul.radios').length > 0) {
		Selected = elem.find('input[type=radio]:checked');
		if (Selected.length === 0) return;
		Forms.Products_Shipping = Selected.parent().attr('data-price');
		return;
	}

	// Dropdown
	if (elem.find('select').length > 0) {
		Selected = elem.find('select option:selected');
		if (Selected.length === 0) return;
		Forms.Products_Shipping = Selected.attr('data-price');
		return;
	}

};

//********************************************************************************* //
