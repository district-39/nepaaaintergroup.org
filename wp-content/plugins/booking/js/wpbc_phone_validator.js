(function () {

	let phone_mask_counter = 0;

	const a = setInterval( function () {

		// Check for jQuery and IMask
		if ( typeof jQuery === 'undefined' || !window.jQuery ) {
			return;
		}

		const wpbc_is_imask_exist = (typeof IMask === 'function');
		const jq_phone            = wpbc_get_jq_phone_fields();
		const wpbc_is_phone_exist = jq_phone.length > 0;

		phone_mask_counter++;

		// Try up to 20 times every 500ms (10s max total)
		if ( phone_mask_counter >= 20 || (wpbc_is_phone_exist && wpbc_is_imask_exist) ) {
			clearInterval( a );  // Stop retries

			// Only run when DOM is ready
			jQuery( document ).ready( function () {
				wpbc_set_phone_mask();
			} );
		}
	}, 500 );
})();


function wpbc_get_jq_phone_fields() {

	return jQuery( '.wpbc_form input[type="tel"], ' +
		'.wpbc_form input[name*="phone" i], ' +
		'.wpbc_form input[name^="fone" i], ' +
		'.wpbc_form input[name^="tel" i], ' +
		'.wpbc_form input[name*="mobile" i], ' +
		'.wpbc_form input[name*="telefono" i], ' +
		'.wpbc_form input[name*="telefone" i], ' +
		'.wpbc_form input[name*="telefon" i]' );
}

// FixIn: 10.12.4.10.
function wpbc_set_phone_mask() {

	var phone_mask_arr = wpbc_get_prepared_phones_mask();
	var default_ISO    = wpbc_guess_country_by_locale();
	var default_mask   = wpbc_get_phone_mask_by_country_ISO( default_ISO );

	if ( !default_mask ) {
		default_mask = {
			mask      : '+___ ___ ___ ____',
			startsWith: '',
			country   : 'Unknown'
		};
	}

	// Ensure default is part of the array
	if ( !phone_mask_arr.some( function (m) {
		return m.mask === default_mask.mask;
	} ) ) {
		phone_mask_arr.push( default_mask );
	}

	/**
	 * Some fields contains a words in any places,  e.g.: name*="phone"
	 * some fields, can  start  only  with these fields:  name^="tel"
	 */
	var jq_phone_fields = wpbc_get_jq_phone_fields();

	jq_phone_fields.each( function () {

		const field_element = this;

		// Clean mask list (convert +{1} to +1 etc.)
		const masks = phone_mask_arr.map( function (m) {
			const cleanedMask = m.mask.replace( /\{|\}/g, '' );
			return {
				mask      : cleanedMask,
				startsWith: m.startsWith,
				country   : m.country,
			};
		} );

		// Get default prefix (e.g. +1)
		const default_prefix = '+' + (default_mask.phonecode || '');  // e.g. "+1"

		// Set placeholder
		const readable_mask = default_mask.mask.replace( /\+\d+/, '+' + default_mask.phonecode );
		field_element.placeholder = readable_mask + (default_mask.iso3 ? ' (' + default_mask.iso3 + ')' : '');

		// Create mask instance
		const imask = IMask( field_element, {
			mask           : masks,
			lazy           : true,
			placeholderChar: '_',       // use underscore instead of default space
			overwrite      : false,
			dispatch       : function (appended, dynamicMasked) {
				const number = (dynamicMasked.value + appended).replace( /\D/g, '' );
				// return dynamicMasked.compiledMasks.find( function (m) {
				// 	return number.startsWith( m.startsWith );
				// } );
				// FixIn: 10.13.1.2.

				return dynamicMasked.compiledMasks
					.filter( function (m) {
						return number.startsWith( m.startsWith );
					} )
					.sort( function (a, b) {
						return b.startsWith.length - a.startsWith.length; // longest prefix first
					} )[0] || dynamicMasked.compiledMasks[(dynamicMasked.compiledMasks.length - 1)]; // return most specific match
			}
		} );

		// // Set default prefix manually and force re-evaluation of mask
		// if ( field_element.value.trim() === '' ) {
		// 	setTimeout( function () {
		// 		imask.value = default_prefix + ' ';  // e.g. "+1 "
		// 		imask.updateValue();
		// 	}, 100 );
		// }

	} );
}



function wpbc_get_prepared_phones_mask() {

	var original_mask_arr = wpbc_get_all_phones_mask();
	var phone_mask_arr    = [];

	for ( var i = 0; i < original_mask_arr.length; i++ ) {
		var maskObj = original_mask_arr[i];
		var newMask = {};

		for ( var key in maskObj ) {
			if ( maskObj.hasOwnProperty( key ) ) {
				newMask[key] = maskObj[key];
			}
		}

		newMask.lazy = false;  // disable lazy mode per mask
		phone_mask_arr.push( newMask );
	}
	return phone_mask_arr;
}


function wpbc_get_all_phones_mask() {
	var phone_mask_arr = [
		{ mask: '+00 000 000 0000', startsWith: '93', country: 'Afghanistan', id: 1, iso: 'AF', name: 'AFGHANISTAN', nicename: 'Afghanistan', iso3: "AFG", numcode: 4, phonecode: 93 },
		{ mask: '+000 000 000 0000', startsWith: '355', country: 'Albania', id: 2, iso: 'AL', name: 'ALBANIA', nicename: 'Albania', iso3: "ALB", numcode: 8, phonecode: 355 },
		{ mask: '+000 000 000 0000', startsWith: '213', country: 'Algeria', id: 3, iso: 'DZ', name: 'ALGERIA', nicename: 'Algeria', iso3: "DZA", numcode: 12, phonecode: 213 },
		{ mask: '+0000 000 000 0000', startsWith: '1684', country: 'American Samoa', id: 4, iso: 'AS', name: 'AMERICAN SAMOA', nicename: 'American Samoa', iso3: "ASM", numcode: 16, phonecode: 1684 },
		{ mask: '+000 000 000 0000', startsWith: '376', country: 'Andorra', id: 5, iso: 'AD', name: 'ANDORRA', nicename: 'Andorra', iso3: "AND", numcode: 20, phonecode: 376 },
		{ mask: '+000 000 000 0000', startsWith: '244', country: 'Angola', id: 6, iso: 'AO', name: 'ANGOLA', nicename: 'Angola', iso3: "AGO", numcode: 24, phonecode: 244 },
		{ mask: '+0000 000 000 0000', startsWith: '1264', country: 'Anguilla', id: 7, iso: 'AI', name: 'ANGUILLA', nicename: 'Anguilla', iso3: "AIA", numcode: 660, phonecode: 1264 },
		{ mask: '+0000 000 000 0000', startsWith: '1268', country: 'Antigua and Barbuda', id: 9, iso: 'AG', name: 'ANTIGUA AND BARBUDA', nicename: 'Antigua and Barbuda', iso3: "ATG", numcode: 28, phonecode: 1268 },
		{ mask: '+00 000 000 0000', startsWith: '54', country: 'Argentina', id: 10, iso: 'AR', name: 'ARGENTINA', nicename: 'Argentina', iso3: "ARG", numcode: 32, phonecode: 54 },
		{ mask: '+000 000 000 0000', startsWith: '374', country: 'Armenia', id: 11, iso: 'AM', name: 'ARMENIA', nicename: 'Armenia', iso3: "ARM", numcode: 51, phonecode: 374 },
		{ mask: '+000 000 000 0000', startsWith: '297', country: 'Aruba', id: 12, iso: 'AW', name: 'ARUBA', nicename: 'Aruba', iso3: "ABW", numcode: 533, phonecode: 297 },
		{ mask: '+00 000 000 0000', startsWith: '61', country: 'Australia', id: 13, iso: 'AU', name: 'AUSTRALIA', nicename: 'Australia', iso3: "AUS", numcode: 36, phonecode: 61 },
		{ mask: '+00 000 000 0000', startsWith: '43', country: 'Austria', id: 14, iso: 'AT', name: 'AUSTRIA', nicename: 'Austria', iso3: "AUT", numcode: 40, phonecode: 43 },
		{ mask: '+000 000 000 0000', startsWith: '994', country: 'Azerbaijan', id: 15, iso: 'AZ', name: 'AZERBAIJAN', nicename: 'Azerbaijan', iso3: "AZE", numcode: 31, phonecode: 994 },
		{ mask: '+0000 000 000 0000', startsWith: '1242', country: 'Bahamas', id: 16, iso: 'BS', name: 'BAHAMAS', nicename: 'Bahamas', iso3: "BHS", numcode: 44, phonecode: 1242 },
		{ mask: '+000 000 000 0000', startsWith: '973', country: 'Bahrain', id: 17, iso: 'BH', name: 'BAHRAIN', nicename: 'Bahrain', iso3: "BHR", numcode: 48, phonecode: 973 },
		{ mask: '+000 000 000 0000', startsWith: '880', country: 'Bangladesh', id: 18, iso: 'BD', name: 'BANGLADESH', nicename: 'Bangladesh', iso3: "BGD", numcode: 50, phonecode: 880 },
		{ mask: '+0000 000 000 0000', startsWith: '1246', country: 'Barbados', id: 19, iso: 'BB', name: 'BARBADOS', nicename: 'Barbados', iso3: "BRB", numcode: 52, phonecode: 1246 },
		{ mask: '+000 000 000 0000', startsWith: '375', country: 'Belarus', id: 20, iso: 'BY', name: 'BELARUS', nicename: 'Belarus', iso3: "BLR", numcode: 112, phonecode: 375 },
		{ mask: '+00 000 000 0000', startsWith: '32', country: 'Belgium', id: 21, iso: 'BE', name: 'BELGIUM', nicename: 'Belgium', iso3: "BEL", numcode: 56, phonecode: 32 },
		{ mask: '+000 000 000 0000', startsWith: '501', country: 'Belize', id: 22, iso: 'BZ', name: 'BELIZE', nicename: 'Belize', iso3: "BLZ", numcode: 84, phonecode: 501 },
		{ mask: '+000 000 000 0000', startsWith: '229', country: 'Benin', id: 23, iso: 'BJ', name: 'BENIN', nicename: 'Benin', iso3: "BEN", numcode: 204, phonecode: 229 },
		{ mask: '+0000 000 000 0000', startsWith: '1441', country: 'Bermuda', id: 24, iso: 'BM', name: 'BERMUDA', nicename: 'Bermuda', iso3: "BMU", numcode: 60, phonecode: 1441 },
		{ mask: '+000 000 000 0000', startsWith: '975', country: 'Bhutan', id: 25, iso: 'BT', name: 'BHUTAN', nicename: 'Bhutan', iso3: "BTN", numcode: 64, phonecode: 975 },
		{ mask: '+000 000 000 0000', startsWith: '591', country: 'Bolivia', id: 26, iso: 'BO', name: 'BOLIVIA', nicename: 'Bolivia', iso3: "BOL", numcode: 68, phonecode: 591 },
		{ mask: '+000 000 000 0000', startsWith: '387', country: 'Bosnia and Herzegovina', id: 27, iso: 'BA', name: 'BOSNIA AND HERZEGOVINA', nicename: 'Bosnia and Herzegovina', iso3: "BIH", numcode: 70, phonecode: 387 },
		{ mask: '+000 000 000 0000', startsWith: '267', country: 'Botswana', id: 28, iso: 'BW', name: 'BOTSWANA', nicename: 'Botswana', iso3: "BWA", numcode: 72, phonecode: 267 },
		{ mask: '+00 000 000 0000', startsWith: '55', country: 'Brazil', id: 30, iso: 'BR', name: 'BRAZIL', nicename: 'Brazil', iso3: "BRA", numcode: 76, phonecode: 55 },
		{ mask: '+000 000 000 0000', startsWith: '246', country: 'British Indian Ocean Territory', id: 31, iso: 'IO', name: 'BRITISH INDIAN OCEAN TERRITORY', nicename: 'British Indian Ocean Territory', iso3: null, numcode: null, phonecode: 246 },
		{ mask: '+000 000 000 0000', startsWith: '673', country: 'Brunei Darussalam', id: 32, iso: 'BN', name: 'BRUNEI DARUSSALAM', nicename: 'Brunei Darussalam', iso3: "BRN", numcode: 96, phonecode: 673 },
		{ mask: '+000 000 000 0000', startsWith: '359', country: 'Bulgaria', id: 33, iso: 'BG', name: 'BULGARIA', nicename: 'Bulgaria', iso3: "BGR", numcode: 100, phonecode: 359 },
		{ mask: '+000 000 000 0000', startsWith: '226', country: 'Burkina Faso', id: 34, iso: 'BF', name: 'BURKINA FASO', nicename: 'Burkina Faso', iso3: "BFA", numcode: 854, phonecode: 226 },
		{ mask: '+000 000 000 0000', startsWith: '257', country: 'Burundi', id: 35, iso: 'BI', name: 'BURUNDI', nicename: 'Burundi', iso3: "BDI", numcode: 108, phonecode: 257 },
		{ mask: '+000 000 000 0000', startsWith: '855', country: 'Cambodia', id: 36, iso: 'KH', name: 'CAMBODIA', nicename: 'Cambodia', iso3: "KHM", numcode: 116, phonecode: 855 },
		{ mask: '+000 000 000 0000', startsWith: '237', country: 'Cameroon', id: 37, iso: 'CM', name: 'CAMEROON', nicename: 'Cameroon', iso3: "CMR", numcode: 120, phonecode: 237 },
		{ mask: '+0 000 000 0000', startsWith: '1', country: 'Canada', id: 38, iso: 'CA', name: 'CANADA', nicename: 'Canada', iso3: "CAN", numcode: 124, phonecode: 1 },
		{ mask: '+000 000 000 0000', startsWith: '238', country: 'Cape Verde', id: 39, iso: 'CV', name: 'CAPE VERDE', nicename: 'Cape Verde', iso3: "CPV", numcode: 132, phonecode: 238 },
		{ mask: '+0000 000 000 0000', startsWith: '1345', country: 'Cayman Islands', id: 40, iso: 'KY', name: 'CAYMAN ISLANDS', nicename: 'Cayman Islands', iso3: "CYM", numcode: 136, phonecode: 1345 },
		{ mask: '+000 000 000 0000', startsWith: '236', country: 'Central African Republic', id: 41, iso: 'CF', name: 'CENTRAL AFRICAN REPUBLIC', nicename: 'Central African Republic', iso3: "CAF", numcode: 140, phonecode: 236 },
		{ mask: '+000 000 000 0000', startsWith: '235', country: 'Chad', id: 42, iso: 'TD', name: 'CHAD', nicename: 'Chad', iso3: "TCD", numcode: 148, phonecode: 235 },
		{ mask: '+00 000 000 0000', startsWith: '56', country: 'Chile', id: 43, iso: 'CL', name: 'CHILE', nicename: 'Chile', iso3: "CHL", numcode: 152, phonecode: 56 },
		{ mask: '+00 000 000 0000', startsWith: '86', country: 'China', id: 44, iso: 'CN', name: 'CHINA', nicename: 'China', iso3: "CHN", numcode: 156, phonecode: 86 },
		{ mask: '+00 000 000 0000', startsWith: '61', country: 'Christmas Island', id: 45, iso: 'CX', name: 'CHRISTMAS ISLAND', nicename: 'Christmas Island', iso3: null, numcode: null, phonecode: 61 },
		{ mask: '+000 000 000 0000', startsWith: '672', country: 'Cocos (Keeling) Islands', id: 46, iso: 'CC', name: 'COCOS (KEELING) ISLANDS', nicename: 'Cocos (Keeling) Islands', iso3: null, numcode: null, phonecode: 672 },
		{ mask: '+00 000 000 0000', startsWith: '57', country: 'Colombia', id: 47, iso: 'CO', name: 'COLOMBIA', nicename: 'Colombia', iso3: "COL", numcode: 170, phonecode: 57 },
		{ mask: '+000 000 000 0000', startsWith: '269', country: 'Comoros', id: 48, iso: 'KM', name: 'COMOROS', nicename: 'Comoros', iso3: "COM", numcode: 174, phonecode: 269 },
		{ mask: '+000 000 000 0000', startsWith: '242', country: 'Congo', id: 49, iso: 'CG', name: 'CONGO', nicename: 'Congo', iso3: "COG", numcode: 178, phonecode: 242 },
		{ mask: '+000 000 000 0000', startsWith: '242', country: 'Congo, the Democratic Republic of the', id: 50, iso: 'CD', name: 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', nicename: 'Congo, the Democratic Republic of the', iso3: "COD", numcode: 180, phonecode: 242 },
		{ mask: '+000 000 000 0000', startsWith: '682', country: 'Cook Islands', id: 51, iso: 'CK', name: 'COOK ISLANDS', nicename: 'Cook Islands', iso3: "COK", numcode: 184, phonecode: 682 },
		{ mask: '+000 000 000 0000', startsWith: '506', country: 'Costa Rica', id: 52, iso: 'CR', name: 'COSTA RICA', nicename: 'Costa Rica', iso3: "CRI", numcode: 188, phonecode: 506 },
		{ mask: '+000 000 000 0000', startsWith: '385', country: 'Croatia', id: 54, iso: 'HR', name: 'CROATIA', nicename: 'Croatia', iso3: "HRV", numcode: 191, phonecode: 385 },
		{ mask: '+00 000 000 0000', startsWith: '53', country: 'Cuba', id: 55, iso: 'CU', name: 'CUBA', nicename: 'Cuba', iso3: "CUB", numcode: 192, phonecode: 53 },
		{ mask: '+000 000 000 0000', startsWith: '357', country: 'Cyprus', id: 56, iso: 'CY', name: 'CYPRUS', nicename: 'Cyprus', iso3: "CYP", numcode: 196, phonecode: 357 },
		{ mask: '+000 000 000 0000', startsWith: '420', country: 'Czech Republic', id: 57, iso: 'CZ', name: 'CZECH REPUBLIC', nicename: 'Czech Republic', iso3: "CZE", numcode: 203, phonecode: 420 },
		{ mask: '+00 000 000 0000', startsWith: '45', country: 'Denmark', id: 58, iso: 'DK', name: 'DENMARK', nicename: 'Denmark', iso3: "DNK", numcode: 208, phonecode: 45 },
		{ mask: '+000 000 000 0000', startsWith: '253', country: 'Djibouti', id: 59, iso: 'DJ', name: 'DJIBOUTI', nicename: 'Djibouti', iso3: "DJI", numcode: 262, phonecode: 253 },
		{ mask: '+0000 000 000 0000', startsWith: '1767', country: 'Dominica', id: 60, iso: 'DM', name: 'DOMINICA', nicename: 'Dominica', iso3: "DMA", numcode: 212, phonecode: 1767 },
		{ mask: '+0000 000 000 0000', startsWith: '1809', country: 'Dominican Republic', id: 61, iso: 'DO', name: 'DOMINICAN REPUBLIC', nicename: 'Dominican Republic', iso3: "DOM", numcode: 214, phonecode: 1809 },
		{ mask: '+000 000 000 0000', startsWith: '593', country: 'Ecuador', id: 62, iso: 'EC', name: 'ECUADOR', nicename: 'Ecuador', iso3: "ECU", numcode: 218, phonecode: 593 },
		{ mask: '+00 000 000 0000', startsWith: '20', country: 'Egypt', id: 63, iso: 'EG', name: 'EGYPT', nicename: 'Egypt', iso3: "EGY", numcode: 818, phonecode: 20 },
		{ mask: '+000 000 000 0000', startsWith: '503', country: 'El Salvador', id: 64, iso: 'SV', name: 'EL SALVADOR', nicename: 'El Salvador', iso3: "SLV", numcode: 222, phonecode: 503 },
		{ mask: '+000 000 000 0000', startsWith: '240', country: 'Equatorial Guinea', id: 65, iso: 'GQ', name: 'EQUATORIAL GUINEA', nicename: 'Equatorial Guinea', iso3: "GNQ", numcode: 226, phonecode: 240 },
		{ mask: '+000 000 000 0000', startsWith: '291', country: 'Eritrea', id: 66, iso: 'ER', name: 'ERITREA', nicename: 'Eritrea', iso3: "ERI", numcode: 232, phonecode: 291 },
		{ mask: '+000 000 000 0000', startsWith: '372', country: 'Estonia', id: 67, iso: 'EE', name: 'ESTONIA', nicename: 'Estonia', iso3: "EST", numcode: 233, phonecode: 372 },
		{ mask: '+000 000 000 0000', startsWith: '251', country: 'Ethiopia', id: 68, iso: 'ET', name: 'ETHIOPIA', nicename: 'Ethiopia', iso3: "ETH", numcode: 231, phonecode: 251 },
		{ mask: '+000 000 000 0000', startsWith: '500', country: 'Falkland Islands (Malvinas)', id: 69, iso: 'FK', name: 'FALKLAND ISLANDS (MALVINAS)', nicename: 'Falkland Islands (Malvinas)', iso3: "FLK", numcode: 238, phonecode: 500 },
		{ mask: '+000 000 000 0000', startsWith: '298', country: 'Faroe Islands', id: 70, iso: 'FO', name: 'FAROE ISLANDS', nicename: 'Faroe Islands', iso3: "FRO", numcode: 234, phonecode: 298 },
		{ mask: '+000 000 000 0000', startsWith: '679', country: 'Fiji', id: 71, iso: 'FJ', name: 'FIJI', nicename: 'Fiji', iso3: "FJI", numcode: 242, phonecode: 679 },
		{ mask: '+000 000 000 0000', startsWith: '358', country: 'Finland', id: 72, iso: 'FI', name: 'FINLAND', nicename: 'Finland', iso3: "FIN", numcode: 246, phonecode: 358 },
		{ mask: '+00 000 000 0000', startsWith: '33', country: 'France', id: 73, iso: 'FR', name: 'FRANCE', nicename: 'France', iso3: "FRA", numcode: 250, phonecode: 33 },
		{ mask: '+000 000 000 0000', startsWith: '594', country: 'French Guiana', id: 74, iso: 'GF', name: 'FRENCH GUIANA', nicename: 'French Guiana', iso3: "GUF", numcode: 254, phonecode: 594 },
		{ mask: '+000 000 000 0000', startsWith: '689', country: 'French Polynesia', id: 75, iso: 'PF', name: 'FRENCH POLYNESIA', nicename: 'French Polynesia', iso3: "PYF", numcode: 258, phonecode: 689 },
		{ mask: '+000 000 000 0000', startsWith: '241', country: 'Gabon', id: 77, iso: 'GA', name: 'GABON', nicename: 'Gabon', iso3: "GAB", numcode: 266, phonecode: 241 },
		{ mask: '+000 000 000 0000', startsWith: '220', country: 'Gambia', id: 78, iso: 'GM', name: 'GAMBIA', nicename: 'Gambia', iso3: "GMB", numcode: 270, phonecode: 220 },
		{ mask: '+000 000 000 0000', startsWith: '995', country: 'Georgia', id: 79, iso: 'GE', name: 'GEORGIA', nicename: 'Georgia', iso3: "GEO", numcode: 268, phonecode: 995 },
		{ mask: '+00 000 000 0000', startsWith: '49', country: 'Germany', id: 80, iso: 'DE', name: 'GERMANY', nicename: 'Germany', iso3: "DEU", numcode: 276, phonecode: 49 },
		{ mask: '+000 000 000 0000', startsWith: '233', country: 'Ghana', id: 81, iso: 'GH', name: 'GHANA', nicename: 'Ghana', iso3: "GHA", numcode: 288, phonecode: 233 },
		{ mask: '+000 000 000 0000', startsWith: '350', country: 'Gibraltar', id: 82, iso: 'GI', name: 'GIBRALTAR', nicename: 'Gibraltar', iso3: "GIB", numcode: 292, phonecode: 350 },
		{ mask: '+00 000 000 0000', startsWith: '30', country: 'Greece', id: 83, iso: 'GR', name: 'GREECE', nicename: 'Greece', iso3: "GRC", numcode: 300, phonecode: 30 },
		{ mask: '+000 000 000 0000', startsWith: '299', country: 'Greenland', id: 84, iso: 'GL', name: 'GREENLAND', nicename: 'Greenland', iso3: "GRL", numcode: 304, phonecode: 299 },
		{ mask: '+0000 000 000 0000', startsWith: '1473', country: 'Grenada', id: 85, iso: 'GD', name: 'GRENADA', nicename: 'Grenada', iso3: "GRD", numcode: 308, phonecode: 1473 },
		{ mask: '+000 000 000 0000', startsWith: '590', country: 'Guadeloupe', id: 86, iso: 'GP', name: 'GUADELOUPE', nicename: 'Guadeloupe', iso3: "GLP", numcode: 312, phonecode: 590 },
		{ mask: '+0000 000 000 0000', startsWith: '1671', country: 'Guam', id: 87, iso: 'GU', name: 'GUAM', nicename: 'Guam', iso3: "GUM", numcode: 316, phonecode: 1671 },
		{ mask: '+000 000 000 0000', startsWith: '502', country: 'Guatemala', id: 88, iso: 'GT', name: 'GUATEMALA', nicename: 'Guatemala', iso3: "GTM", numcode: 320, phonecode: 502 },
		{ mask: '+000 000 000 0000', startsWith: '224', country: 'Guinea', id: 89, iso: 'GN', name: 'GUINEA', nicename: 'Guinea', iso3: "GIN", numcode: 324, phonecode: 224 },
		{ mask: '+000 000 000 0000', startsWith: '245', country: 'Guinea-Bissau', id: 90, iso: 'GW', name: 'GUINEA-BISSAU', nicename: 'Guinea-Bissau', iso3: "GNB", numcode: 624, phonecode: 245 },
		{ mask: '+000 000 000 0000', startsWith: '592', country: 'Guyana', id: 91, iso: 'GY', name: 'GUYANA', nicename: 'Guyana', iso3: "GUY", numcode: 328, phonecode: 592 },
		{ mask: '+000 000 000 0000', startsWith: '509', country: 'Haiti', id: 92, iso: 'HT', name: 'HAITI', nicename: 'Haiti', iso3: "HTI", numcode: 332, phonecode: 509 },
		{ mask: '+00 000 000 0000', startsWith: '39', country: 'Holy See (Vatican City State)', id: 94, iso: 'VA', name: 'HOLY SEE (VATICAN CITY STATE)', nicename: 'Holy See (Vatican City State)', iso3: "VAT", numcode: 336, phonecode: 39 },
		{ mask: '+000 000 000 0000', startsWith: '504', country: 'Honduras', id: 95, iso: 'HN', name: 'HONDURAS', nicename: 'Honduras', iso3: "HND", numcode: 340, phonecode: 504 },
		{ mask: '+000 000 000 0000', startsWith: '852', country: 'Hong Kong', id: 96, iso: 'HK', name: 'HONG KONG', nicename: 'Hong Kong', iso3: "HKG", numcode: 344, phonecode: 852 },
		{ mask: '+00 000 000 0000', startsWith: '36', country: 'Hungary', id: 97, iso: 'HU', name: 'HUNGARY', nicename: 'Hungary', iso3: "HUN", numcode: 348, phonecode: 36 },
		{ mask: '+000 000 000 0000', startsWith: '354', country: 'Iceland', id: 98, iso: 'IS', name: 'ICELAND', nicename: 'Iceland', iso3: "ISL", numcode: 352, phonecode: 354 },
		{ mask: '+00 000 000 0000', startsWith: '91', country: 'India', id: 99, iso: 'IN', name: 'INDIA', nicename: 'India', iso3: "IND", numcode: 356, phonecode: 91 },
		{ mask: '+00 000 000 0000', startsWith: '62', country: 'Indonesia', id: 100, iso: 'ID', name: 'INDONESIA', nicename: 'Indonesia', iso3: "IDN", numcode: 360, phonecode: 62 },
		{ mask: '+00 000 000 0000', startsWith: '98', country: 'Iran, Islamic Republic of', id: 101, iso: 'IR', name: 'IRAN, ISLAMIC REPUBLIC OF', nicename: 'Iran, Islamic Republic of', iso3: "IRN", numcode: 364, phonecode: 98 },
		{ mask: '+000 000 000 0000', startsWith: '964', country: 'Iraq', id: 102, iso: 'IQ', name: 'IRAQ', nicename: 'Iraq', iso3: "IRQ", numcode: 368, phonecode: 964 },
		{ mask: '+000 000 000 0000', startsWith: '353', country: 'Ireland', id: 103, iso: 'IE', name: 'IRELAND', nicename: 'Ireland', iso3: "IRL", numcode: 372, phonecode: 353 },
		{ mask: '+000 000 000 0000', startsWith: '972', country: 'Israel', id: 104, iso: 'IL', name: 'ISRAEL', nicename: 'Israel', iso3: "ISR", numcode: 376, phonecode: 972 },
		{ mask: '+00 000 000 0000', startsWith: '39', country: 'Italy', id: 105, iso: 'IT', name: 'ITALY', nicename: 'Italy', iso3: "ITA", numcode: 380, phonecode: 39 },
		{ mask: '+0000 000 000 0000', startsWith: '1876', country: 'Jamaica', id: 106, iso: 'JM', name: 'JAMAICA', nicename: 'Jamaica', iso3: "JAM", numcode: 388, phonecode: 1876 },
		{ mask: '+00 000 000 0000', startsWith: '81', country: 'Japan', id: 107, iso: 'JP', name: 'JAPAN', nicename: 'Japan', iso3: "JPN", numcode: 392, phonecode: 81 },
		{ mask: '+000 000 000 0000', startsWith: '962', country: 'Jordan', id: 108, iso: 'JO', name: 'JORDAN', nicename: 'Jordan', iso3: "JOR", numcode: 400, phonecode: 962 },
		{ mask: '+0 000 000 0000', startsWith: '7', country: 'Kazakhstan', id: 109, iso: 'KZ', name: 'KAZAKHSTAN', nicename: 'Kazakhstan', iso3: "KAZ", numcode: 398, phonecode: 7 },
		{ mask: '+000 000 000 0000', startsWith: '254', country: 'Kenya', id: 110, iso: 'KE', name: 'KENYA', nicename: 'Kenya', iso3: "KEN", numcode: 404, phonecode: 254 },
		{ mask: '+000 000 000 0000', startsWith: '686', country: 'Kiribati', id: 111, iso: 'KI', name: 'KIRIBATI', nicename: 'Kiribati', iso3: "KIR", numcode: 296, phonecode: 686 },
		{ mask: '+00 000 000 0000', startsWith: '82', country: 'Korea, Republic of', id: 113, iso: 'KR', name: 'KOREA, REPUBLIC OF', nicename: 'Korea, Republic of', iso3: "KOR", numcode: 410, phonecode: 82 },
		{ mask: '+000 000 000 0000', startsWith: '965', country: 'Kuwait', id: 114, iso: 'KW', name: 'KUWAIT', nicename: 'Kuwait', iso3: "KWT", numcode: 414, phonecode: 965 },
		{ mask: '+000 000 000 0000', startsWith: '996', country: 'Kyrgyzstan', id: 115, iso: 'KG', name: 'KYRGYZSTAN', nicename: 'Kyrgyzstan', iso3: "KGZ", numcode: 417, phonecode: 996 },
		{ mask: '+000 000 000 0000', startsWith: '371', country: 'Latvia', id: 117, iso: 'LV', name: 'LATVIA', nicename: 'Latvia', iso3: "LVA", numcode: 428, phonecode: 371 },
		{ mask: '+000 000 000 0000', startsWith: '961', country: 'Lebanon', id: 118, iso: 'LB', name: 'LEBANON', nicename: 'Lebanon', iso3: "LBN", numcode: 422, phonecode: 961 },
		{ mask: '+000 000 000 0000', startsWith: '266', country: 'Lesotho', id: 119, iso: 'LS', name: 'LESOTHO', nicename: 'Lesotho', iso3: "LSO", numcode: 426, phonecode: 266 },
		{ mask: '+000 000 000 0000', startsWith: '231', country: 'Liberia', id: 120, iso: 'LR', name: 'LIBERIA', nicename: 'Liberia', iso3: "LBR", numcode: 430, phonecode: 231 },
		{ mask: '+000 000 000 0000', startsWith: '218', country: 'Libyan Arab Jamahiriya', id: 121, iso: 'LY', name: 'LIBYAN ARAB JAMAHIRIYA', nicename: 'Libyan Arab Jamahiriya', iso3: "LBY", numcode: 434, phonecode: 218 },
		{ mask: '+000 000 000 0000', startsWith: '423', country: 'Liechtenstein', id: 122, iso: 'LI', name: 'LIECHTENSTEIN', nicename: 'Liechtenstein', iso3: "LIE", numcode: 438, phonecode: 423 },
		{ mask: '+000 000 000 0000', startsWith: '370', country: 'Lithuania', id: 123, iso: 'LT', name: 'LITHUANIA', nicename: 'Lithuania', iso3: "LTU", numcode: 440, phonecode: 370 },
		{ mask: '+000 000 000 0000', startsWith: '352', country: 'Luxembourg', id: 124, iso: 'LU', name: 'LUXEMBOURG', nicename: 'Luxembourg', iso3: "LUX", numcode: 442, phonecode: 352 },
		{ mask: '+000 000 000 0000', startsWith: '853', country: 'Macao', id: 125, iso: 'MO', name: 'MACAO', nicename: 'Macao', iso3: "MAC", numcode: 446, phonecode: 853 },
		{ mask: '+000 000 000 0000', startsWith: '389', country: 'Macedonia, the Former Yugoslav Republic of', id: 126, iso: 'MK', name: 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', nicename: 'Macedonia, the Former Yugoslav Republic of', iso3: "MKD", numcode: 807, phonecode: 389 },
		{ mask: '+000 000 000 0000', startsWith: '261', country: 'Madagascar', id: 127, iso: 'MG', name: 'MADAGASCAR', nicename: 'Madagascar', iso3: "MDG", numcode: 450, phonecode: 261 },
		{ mask: '+000 000 000 0000', startsWith: '265', country: 'Malawi', id: 128, iso: 'MW', name: 'MALAWI', nicename: 'Malawi', iso3: "MWI", numcode: 454, phonecode: 265 },
		{ mask: '+00 000 000 0000', startsWith: '60', country: 'Malaysia', id: 129, iso: 'MY', name: 'MALAYSIA', nicename: 'Malaysia', iso3: "MYS", numcode: 458, phonecode: 60 },
		{ mask: '+000 000 000 0000', startsWith: '960', country: 'Maldives', id: 130, iso: 'MV', name: 'MALDIVES', nicename: 'Maldives', iso3: "MDV", numcode: 462, phonecode: 960 },
		{ mask: '+000 000 000 0000', startsWith: '223', country: 'Mali', id: 131, iso: 'ML', name: 'MALI', nicename: 'Mali', iso3: "MLI", numcode: 466, phonecode: 223 },
		{ mask: '+000 000 000 0000', startsWith: '356', country: 'Malta', id: 132, iso: 'MT', name: 'MALTA', nicename: 'Malta', iso3: "MLT", numcode: 470, phonecode: 356 },
		{ mask: '+000 000 000 0000', startsWith: '692', country: 'Marshall Islands', id: 133, iso: 'MH', name: 'MARSHALL ISLANDS', nicename: 'Marshall Islands', iso3: "MHL", numcode: 584, phonecode: 692 },
		{ mask: '+000 000 000 0000', startsWith: '596', country: 'Martinique', id: 134, iso: 'MQ', name: 'MARTINIQUE', nicename: 'Martinique', iso3: "MTQ", numcode: 474, phonecode: 596 },
		{ mask: '+000 000 000 0000', startsWith: '222', country: 'Mauritania', id: 135, iso: 'MR', name: 'MAURITANIA', nicename: 'Mauritania', iso3: "MRT", numcode: 478, phonecode: 222 },
		{ mask: '+000 000 000 0000', startsWith: '230', country: 'Mauritius', id: 136, iso: 'MU', name: 'MAURITIUS', nicename: 'Mauritius', iso3: "MUS", numcode: 480, phonecode: 230 },
		{ mask: '+000 000 000 0000', startsWith: '269', country: 'Mayotte', id: 137, iso: 'YT', name: 'MAYOTTE', nicename: 'Mayotte', iso3: null, numcode: null, phonecode: 269 },
		{ mask: '+00 000 000 0000', startsWith: '52', country: 'Mexico', id: 138, iso: 'MX', name: 'MEXICO', nicename: 'Mexico', iso3: "MEX", numcode: 484, phonecode: 52 },
		{ mask: '+000 000 000 0000', startsWith: '691', country: 'Micronesia, Federated States of', id: 139, iso: 'FM', name: 'MICRONESIA, FEDERATED STATES OF', nicename: 'Micronesia, Federated States of', iso3: "FSM", numcode: 583, phonecode: 691 },
		{ mask: '+000 000 000 0000', startsWith: '373', country: 'Moldova, Republic of', id: 140, iso: 'MD', name: 'MOLDOVA, REPUBLIC OF', nicename: 'Moldova, Republic of', iso3: "MDA", numcode: 498, phonecode: 373 },
		{ mask: '+000 000 000 0000', startsWith: '377', country: 'Monaco', id: 141, iso: 'MC', name: 'MONACO', nicename: 'Monaco', iso3: "MCO", numcode: 492, phonecode: 377 },
		{ mask: '+000 000 000 0000', startsWith: '976', country: 'Mongolia', id: 142, iso: 'MN', name: 'MONGOLIA', nicename: 'Mongolia', iso3: "MNG", numcode: 496, phonecode: 976 },
		{ mask: '+0000 000 000 0000', startsWith: '1664', country: 'Montserrat', id: 143, iso: 'MS', name: 'MONTSERRAT', nicename: 'Montserrat', iso3: "MSR", numcode: 500, phonecode: 1664 },
		{ mask: '+000 000 000 0000', startsWith: '212', country: 'Morocco', id: 144, iso: 'MA', name: 'MOROCCO', nicename: 'Morocco', iso3: "MAR", numcode: 504, phonecode: 212 },
		{ mask: '+000 000 000 0000', startsWith: '258', country: 'Mozambique', id: 145, iso: 'MZ', name: 'MOZAMBIQUE', nicename: 'Mozambique', iso3: "MOZ", numcode: 508, phonecode: 258 },
		{ mask: '+00 000 000 0000', startsWith: '95', country: 'Myanmar', id: 146, iso: 'MM', name: 'MYANMAR', nicename: 'Myanmar', iso3: "MMR", numcode: 104, phonecode: 95 },
		{ mask: '+000 000 000 0000', startsWith: '264', country: 'Namibia', id: 147, iso: 'NA', name: 'NAMIBIA', nicename: 'Namibia', iso3: "NAM", numcode: 516, phonecode: 264 },
		{ mask: '+000 000 000 0000', startsWith: '674', country: 'Nauru', id: 148, iso: 'NR', name: 'NAURU', nicename: 'Nauru', iso3: "NRU", numcode: 520, phonecode: 674 },
		{ mask: '+000 000 000 0000', startsWith: '977', country: 'Nepal', id: 149, iso: 'NP', name: 'NEPAL', nicename: 'Nepal', iso3: "NPL", numcode: 524, phonecode: 977 },
		{ mask: '+00 000 000 0000', startsWith: '31', country: 'Netherlands', id: 150, iso: 'NL', name: 'NETHERLANDS', nicename: 'Netherlands', iso3: "NLD", numcode: 528, phonecode: 31 },
		{ mask: '+000 000 000 0000', startsWith: '599', country: 'Netherlands Antilles', id: 151, iso: 'AN', name: 'NETHERLANDS ANTILLES', nicename: 'Netherlands Antilles', iso3: "ANT", numcode: 530, phonecode: 599 },
		{ mask: '+000 000 000 0000', startsWith: '687', country: 'New Caledonia', id: 152, iso: 'NC', name: 'NEW CALEDONIA', nicename: 'New Caledonia', iso3: "NCL", numcode: 540, phonecode: 687 },
		{ mask: '+00 000 000 0000', startsWith: '64', country: 'New Zealand', id: 153, iso: 'NZ', name: 'NEW ZEALAND', nicename: 'New Zealand', iso3: "NZL", numcode: 554, phonecode: 64 },
		{ mask: '+000 000 000 0000', startsWith: '505', country: 'Nicaragua', id: 154, iso: 'NI', name: 'NICARAGUA', nicename: 'Nicaragua', iso3: "NIC", numcode: 558, phonecode: 505 },
		{ mask: '+000 000 000 0000', startsWith: '227', country: 'Niger', id: 155, iso: 'NE', name: 'NIGER', nicename: 'Niger', iso3: "NER", numcode: 562, phonecode: 227 },
		{ mask: '+000 000 000 0000', startsWith: '234', country: 'Nigeria', id: 156, iso: 'NG', name: 'NIGERIA', nicename: 'Nigeria', iso3: "NGA", numcode: 566, phonecode: 234 },
		{ mask: '+000 000 000 0000', startsWith: '683', country: 'Niue', id: 157, iso: 'NU', name: 'NIUE', nicename: 'Niue', iso3: "NIU", numcode: 570, phonecode: 683 },
		{ mask: '+000 000 000 0000', startsWith: '672', country: 'Norfolk Island', id: 158, iso: 'NF', name: 'NORFOLK ISLAND', nicename: 'Norfolk Island', iso3: "NFK", numcode: 574, phonecode: 672 },
		{ mask: '+0000 000 000 0000', startsWith: '1670', country: 'Northern Mariana Islands', id: 159, iso: 'MP', name: 'NORTHERN MARIANA ISLANDS', nicename: 'Northern Mariana Islands', iso3: "MNP", numcode: 580, phonecode: 1670 },
		{ mask: '+00 000 000 0000', startsWith: '47', country: 'Norway', id: 160, iso: 'NO', name: 'NORWAY', nicename: 'Norway', iso3: "NOR", numcode: 578, phonecode: 47 },
		{ mask: '+000 000 000 0000', startsWith: '968', country: 'Oman', id: 161, iso: 'OM', name: 'OMAN', nicename: 'Oman', iso3: "OMN", numcode: 512, phonecode: 968 },
		{ mask: '+00 000 000 0000', startsWith: '92', country: 'Pakistan', id: 162, iso: 'PK', name: 'PAKISTAN', nicename: 'Pakistan', iso3: "PAK", numcode: 586, phonecode: 92 },
		{ mask: '+000 000 000 0000', startsWith: '680', country: 'Palau', id: 163, iso: 'PW', name: 'PALAU', nicename: 'Palau', iso3: "PLW", numcode: 585, phonecode: 680 },
		{ mask: '+000 000 000 0000', startsWith: '970', country: 'Palestinian Territory, Occupied', id: 164, iso: 'PS', name: 'PALESTINIAN TERRITORY, OCCUPIED', nicename: 'Palestinian Territory, Occupied', iso3: null, numcode: null, phonecode: 970 },
		{ mask: '+000 000 000 0000', startsWith: '507', country: 'Panama', id: 165, iso: 'PA', name: 'PANAMA', nicename: 'Panama', iso3: "PAN", numcode: 591, phonecode: 507 },
		{ mask: '+000 000 000 0000', startsWith: '675', country: 'Papua New Guinea', id: 166, iso: 'PG', name: 'PAPUA NEW GUINEA', nicename: 'Papua New Guinea', iso3: "PNG", numcode: 598, phonecode: 675 },
		{ mask: '+000 000 000 0000', startsWith: '595', country: 'Paraguay', id: 167, iso: 'PY', name: 'PARAGUAY', nicename: 'Paraguay', iso3: "PRY", numcode: 600, phonecode: 595 },
		{ mask: '+00 000 000 0000', startsWith: '51', country: 'Peru', id: 168, iso: 'PE', name: 'PERU', nicename: 'Peru', iso3: "PER", numcode: 604, phonecode: 51 },
		{ mask: '+00 000 000 0000', startsWith: '63', country: 'Philippines', id: 169, iso: 'PH', name: 'PHILIPPINES', nicename: 'Philippines', iso3: "PHL", numcode: 608, phonecode: 63 },
		{ mask: '+00 000 000 0000', startsWith: '48', country: 'Poland', id: 171, iso: 'PL', name: 'POLAND', nicename: 'Poland', iso3: "POL", numcode: 616, phonecode: 48 },
		{ mask: '+000 000 000 0000', startsWith: '351', country: 'Portugal', id: 172, iso: 'PT', name: 'PORTUGAL', nicename: 'Portugal', iso3: "PRT", numcode: 620, phonecode: 351 },
		{ mask: '+0000 000 000 0000', startsWith: '1787', country: 'Puerto Rico', id: 173, iso: 'PR', name: 'PUERTO RICO', nicename: 'Puerto Rico', iso3: "PRI", numcode: 630, phonecode: 1787 },
		{ mask: '+000 000 000 0000', startsWith: '974', country: 'Qatar', id: 174, iso: 'QA', name: 'QATAR', nicename: 'Qatar', iso3: "QAT", numcode: 634, phonecode: 974 },
		{ mask: '+000 000 000 0000', startsWith: '262', country: 'Reunion', id: 175, iso: 'RE', name: 'REUNION', nicename: 'Reunion', iso3: "REU", numcode: 638, phonecode: 262 },
		{ mask: '+00 000 000 0000', startsWith: '40', country: 'Romania', id: 176, iso: 'RO', name: 'ROMANIA', nicename: 'Romania', iso3: "ROM", numcode: 642, phonecode: 40 },
		{ mask: '+00 000 000 0000', startsWith: '70', country: 'Russian Federation', id: 177, iso: 'RU', name: 'RUSSIAN FEDERATION', nicename: 'Russian Federation', iso3: "RUS", numcode: 643, phonecode: 70 },
		{ mask: '+000 000 000 0000', startsWith: '250', country: 'Rwanda', id: 178, iso: 'RW', name: 'RWANDA', nicename: 'Rwanda', iso3: "RWA", numcode: 646, phonecode: 250 },
		{ mask: '+000 000 000 0000', startsWith: '290', country: 'Saint Helena', id: 179, iso: 'SH', name: 'SAINT HELENA', nicename: 'Saint Helena', iso3: "SHN", numcode: 654, phonecode: 290 },
		{ mask: '+0000 000 000 0000', startsWith: '1869', country: 'Saint Kitts and Nevis', id: 180, iso: 'KN', name: 'SAINT KITTS AND NEVIS', nicename: 'Saint Kitts and Nevis', iso3: "KNA", numcode: 659, phonecode: 1869 },
		{ mask: '+0000 000 000 0000', startsWith: '1758', country: 'Saint Lucia', id: 181, iso: 'LC', name: 'SAINT LUCIA', nicename: 'Saint Lucia', iso3: "LCA", numcode: 662, phonecode: 1758 },
		{ mask: '+000 000 000 0000', startsWith: '508', country: 'Saint Pierre and Miquelon', id: 182, iso: 'PM', name: 'SAINT PIERRE AND MIQUELON', nicename: 'Saint Pierre and Miquelon', iso3: "SPM", numcode: 666, phonecode: 508 },
		{ mask: '+0000 000 000 0000', startsWith: '1784', country: 'Saint Vincent and the Grenadines', id: 183, iso: 'VC', name: 'SAINT VINCENT AND THE GRENADINES', nicename: 'Saint Vincent and the Grenadines', iso3: "VCT", numcode: 670, phonecode: 1784 },
		{ mask: '+000 000 000 0000', startsWith: '684', country: 'Samoa', id: 184, iso: 'WS', name: 'SAMOA', nicename: 'Samoa', iso3: "WSM", numcode: 882, phonecode: 684 },
		{ mask: '+000 000 000 0000', startsWith: '378', country: 'San Marino', id: 185, iso: 'SM', name: 'SAN MARINO', nicename: 'San Marino', iso3: "SMR", numcode: 674, phonecode: 378 },
		{ mask: '+000 000 000 0000', startsWith: '239', country: 'Sao Tome and Principe', id: 186, iso: 'ST', name: 'SAO TOME AND PRINCIPE', nicename: 'Sao Tome and Principe', iso3: "STP", numcode: 678, phonecode: 239 },
		{ mask: '+000 000 000 0000', startsWith: '966', country: 'Saudi Arabia', id: 187, iso: 'SA', name: 'SAUDI ARABIA', nicename: 'Saudi Arabia', iso3: "SAU", numcode: 682, phonecode: 966 },
		{ mask: '+000 000 000 0000', startsWith: '221', country: 'Senegal', id: 188, iso: 'SN', name: 'SENEGAL', nicename: 'Senegal', iso3: "SEN", numcode: 686, phonecode: 221 },
		{ mask: '+000 000 000 0000', startsWith: '381', country: 'Serbia and Montenegro', id: 189, iso: 'CS', name: 'SERBIA AND MONTENEGRO', nicename: 'Serbia and Montenegro', iso3: null, numcode: null, phonecode: 381 },
		{ mask: '+000 000 000 0000', startsWith: '248', country: 'Seychelles', id: 190, iso: 'SC', name: 'SEYCHELLES', nicename: 'Seychelles', iso3: "SYC", numcode: 690, phonecode: 248 },
		{ mask: '+000 000 000 0000', startsWith: '232', country: 'Sierra Leone', id: 191, iso: 'SL', name: 'SIERRA LEONE', nicename: 'Sierra Leone', iso3: "SLE", numcode: 694, phonecode: 232 },
		{ mask: '+00 000 000 0000', startsWith: '65', country: 'Singapore', id: 192, iso: 'SG', name: 'SINGAPORE', nicename: 'Singapore', iso3: "SGP", numcode: 702, phonecode: 65 },
		{ mask: '+000 000 000 0000', startsWith: '421', country: 'Slovakia', id: 193, iso: 'SK', name: 'SLOVAKIA', nicename: 'Slovakia', iso3: "SVK", numcode: 703, phonecode: 421 },
		{ mask: '+000 000 000 0000', startsWith: '386', country: 'Slovenia', id: 194, iso: 'SI', name: 'SLOVENIA', nicename: 'Slovenia', iso3: "SVN", numcode: 705, phonecode: 386 },
		{ mask: '+000 000 000 0000', startsWith: '677', country: 'Solomon Islands', id: 195, iso: 'SB', name: 'SOLOMON ISLANDS', nicename: 'Solomon Islands', iso3: "SLB", numcode: 90, phonecode: 677 },
		{ mask: '+000 000 000 0000', startsWith: '252', country: 'Somalia', id: 196, iso: 'SO', name: 'SOMALIA', nicename: 'Somalia', iso3: "SOM", numcode: 706, phonecode: 252 },
		{ mask: '+00 000 000 0000', startsWith: '27', country: 'South Africa', id: 197, iso: 'ZA', name: 'SOUTH AFRICA', nicename: 'South Africa', iso3: "ZAF", numcode: 710, phonecode: 27 },
		{ mask: '+00 000 000 0000', startsWith: '34', country: 'Spain', id: 199, iso: 'ES', name: 'SPAIN', nicename: 'Spain', iso3: "ESP", numcode: 724, phonecode: 34 },
		{ mask: '+00 000 000 0000', startsWith: '94', country: 'Sri Lanka', id: 200, iso: 'LK', name: 'SRI LANKA', nicename: 'Sri Lanka', iso3: "LKA", numcode: 144, phonecode: 94 },
		{ mask: '+000 000 000 0000', startsWith: '249', country: 'Sudan', id: 201, iso: 'SD', name: 'SUDAN', nicename: 'Sudan', iso3: "SDN", numcode: 736, phonecode: 249 },
		{ mask: '+000 000 000 0000', startsWith: '597', country: 'Suriname', id: 202, iso: 'SR', name: 'SURINAME', nicename: 'Suriname', iso3: "SUR", numcode: 740, phonecode: 597 },
		{ mask: '+00 000 000 0000', startsWith: '47', country: 'Svalbard and Jan Mayen', id: 203, iso: 'SJ', name: 'SVALBARD AND JAN MAYEN', nicename: 'Svalbard and Jan Mayen', iso3: "SJM", numcode: 744, phonecode: 47 },
		{ mask: '+000 000 000 0000', startsWith: '268', country: 'Swaziland', id: 204, iso: 'SZ', name: 'SWAZILAND', nicename: 'Swaziland', iso3: "SWZ", numcode: 748, phonecode: 268 },
		{ mask: '+00 000 000 0000', startsWith: '46', country: 'Sweden', id: 205, iso: 'SE', name: 'SWEDEN', nicename: 'Sweden', iso3: "SWE", numcode: 752, phonecode: 46 },
		{ mask: '+00 000 000 0000', startsWith: '41', country: 'Switzerland', id: 206, iso: 'CH', name: 'SWITZERLAND', nicename: 'Switzerland', iso3: "CHE", numcode: 756, phonecode: 41 },
		{ mask: '+000 000 000 0000', startsWith: '963', country: 'Syrian Arab Republic', id: 207, iso: 'SY', name: 'SYRIAN ARAB REPUBLIC', nicename: 'Syrian Arab Republic', iso3: "SYR", numcode: 760, phonecode: 963 },
		{ mask: '+000 000 000 0000', startsWith: '886', country: 'Taiwan, Province of China', id: 208, iso: 'TW', name: 'TAIWAN, PROVINCE OF CHINA', nicename: 'Taiwan, Province of China', iso3: "TWN", numcode: 158, phonecode: 886 },
		{ mask: '+000 000 000 0000', startsWith: '992', country: 'Tajikistan', id: 209, iso: 'TJ', name: 'TAJIKISTAN', nicename: 'Tajikistan', iso3: "TJK", numcode: 762, phonecode: 992 },
		{ mask: '+000 000 000 0000', startsWith: '255', country: 'Tanzania, United Republic of', id: 210, iso: 'TZ', name: 'TANZANIA, UNITED REPUBLIC OF', nicename: 'Tanzania, United Republic of', iso3: "TZA", numcode: 834, phonecode: 255 },
		{ mask: '+00 000 000 0000', startsWith: '66', country: 'Thailand', id: 211, iso: 'TH', name: 'THAILAND', nicename: 'Thailand', iso3: "THA", numcode: 764, phonecode: 66 },
		{ mask: '+000 000 000 0000', startsWith: '670', country: 'Timor-Leste', id: 212, iso: 'TL', name: 'TIMOR-LESTE', nicename: 'Timor-Leste', iso3: null, numcode: null, phonecode: 670 },
		{ mask: '+000 000 000 0000', startsWith: '228', country: 'Togo', id: 213, iso: 'TG', name: 'TOGO', nicename: 'Togo', iso3: "TGO", numcode: 768, phonecode: 228 },
		{ mask: '+000 000 000 0000', startsWith: '690', country: 'Tokelau', id: 214, iso: 'TK', name: 'TOKELAU', nicename: 'Tokelau', iso3: "TKL", numcode: 772, phonecode: 690 },
		{ mask: '+000 000 000 0000', startsWith: '676', country: 'Tonga', id: 215, iso: 'TO', name: 'TONGA', nicename: 'Tonga', iso3: "TON", numcode: 776, phonecode: 676 },
		{ mask: '+0000 000 000 0000', startsWith: '1868', country: 'Trinidad and Tobago', id: 216, iso: 'TT', name: 'TRINIDAD AND TOBAGO', nicename: 'Trinidad and Tobago', iso3: "TTO", numcode: 780, phonecode: 1868 },
		{ mask: '+000 000 000 0000', startsWith: '216', country: 'Tunisia', id: 217, iso: 'TN', name: 'TUNISIA', nicename: 'Tunisia', iso3: "TUN", numcode: 788, phonecode: 216 },
		{ mask: '+00 000 000 0000', startsWith: '90', country: 'Turkey', id: 218, iso: 'TR', name: 'TURKEY', nicename: 'Turkey', iso3: "TUR", numcode: 792, phonecode: 90 },
		{ mask: '+0000 000 000 0000', startsWith: '7370', country: 'Turkmenistan', id: 219, iso: 'TM', name: 'TURKMENISTAN', nicename: 'Turkmenistan', iso3: "TKM", numcode: 795, phonecode: 7370 },
		{ mask: '+0000 000 000 0000', startsWith: '1649', country: 'Turks and Caicos Islands', id: 220, iso: 'TC', name: 'TURKS AND CAICOS ISLANDS', nicename: 'Turks and Caicos Islands', iso3: "TCA", numcode: 796, phonecode: 1649 },
		{ mask: '+000 000 000 0000', startsWith: '688', country: 'Tuvalu', id: 221, iso: 'TV', name: 'TUVALU', nicename: 'Tuvalu', iso3: "TUV", numcode: 798, phonecode: 688 },
		{ mask: '+000 000 000 0000', startsWith: '256', country: 'Uganda', id: 222, iso: 'UG', name: 'UGANDA', nicename: 'Uganda', iso3: "UGA", numcode: 800, phonecode: 256 },
		{ mask: '+000 00 000 0000', startsWith: '380', country: 'Ukraine', id: 223, iso: 'UA', name: 'UKRAINE', nicename: 'Ukraine', iso3: "UKR", numcode: 804, phonecode: 380 },
		{ mask: '+000 000 000 0000', startsWith: '971', country: 'United Arab Emirates', id: 224, iso: 'AE', name: 'UNITED ARAB EMIRATES', nicename: 'United Arab Emirates', iso3: "ARE", numcode: 784, phonecode: 971 },
		{ mask: '+00 000 000 0000', startsWith: '44', country: 'United Kingdom', id: 225, iso: 'GB', name: 'UNITED KINGDOM', nicename: 'United Kingdom', iso3: "GBR", numcode: 826, phonecode: 44 },
		{ mask: '+0 000 000 0000', startsWith: '1', country: 'United States', id: 226, iso: 'US', name: 'UNITED STATES', nicename: 'United States', iso3: "USA", numcode: 840, phonecode: 1 },
		{ mask: '+0 000 000 0000', startsWith: '1', country: 'United States Minor Outlying Islands', id: 227, iso: 'UM', name: 'UNITED STATES MINOR OUTLYING ISLANDS', nicename: 'United States Minor Outlying Islands', iso3: null, numcode: null, phonecode: 1 },
		{ mask: '+000 000 000 0000', startsWith: '598', country: 'Uruguay', id: 228, iso: 'UY', name: 'URUGUAY', nicename: 'Uruguay', iso3: "URY", numcode: 858, phonecode: 598 },
		{ mask: '+000 000 000 0000', startsWith: '998', country: 'Uzbekistan', id: 229, iso: 'UZ', name: 'UZBEKISTAN', nicename: 'Uzbekistan', iso3: "UZB", numcode: 860, phonecode: 998 },
		{ mask: '+000 000 000 0000', startsWith: '678', country: 'Vanuatu', id: 230, iso: 'VU', name: 'VANUATU', nicename: 'Vanuatu', iso3: "VUT", numcode: 548, phonecode: 678 },
		{ mask: '+00 000 000 0000', startsWith: '58', country: 'Venezuela', id: 231, iso: 'VE', name: 'VENEZUELA', nicename: 'Venezuela', iso3: "VEN", numcode: 862, phonecode: 58 },
		{ mask: '+00 000 000 0000', startsWith: '84', country: 'Viet Nam', id: 232, iso: 'VN', name: 'VIET NAM', nicename: 'Viet Nam', iso3: "VNM", numcode: 704, phonecode: 84 },
		{ mask: '+0000 000 000 0000', startsWith: '1284', country: 'Virgin Islands, British', id: 233, iso: 'VG', name: 'VIRGIN ISLANDS, BRITISH', nicename: 'Virgin Islands, British', iso3: "VGB", numcode: 92, phonecode: 1284 },
		{ mask: '+0000 000 000 0000', startsWith: '1340', country: 'Virgin Islands, U.s.', id: 234, iso: 'VI', name: 'VIRGIN ISLANDS, U.S.', nicename: 'Virgin Islands, U.s.', iso3: "VIR", numcode: 850, phonecode: 1340 },
		{ mask: '+000 000 000 0000', startsWith: '681', country: 'Wallis and Futuna', id: 235, iso: 'WF', name: 'WALLIS AND FUTUNA', nicename: 'Wallis and Futuna', iso3: "WLF", numcode: 876, phonecode: 681 },
		{ mask: '+000 000 000 0000', startsWith: '212', country: 'Western Sahara', id: 236, iso: 'EH', name: 'WESTERN SAHARA', nicename: 'Western Sahara', iso3: "ESH", numcode: 732, phonecode: 212 },
		{ mask: '+000 000 000 0000', startsWith: '967', country: 'Yemen', id: 237, iso: 'YE', name: 'YEMEN', nicename: 'Yemen', iso3: "YEM", numcode: 887, phonecode: 967 },
		{ mask: '+000 000 000 0000', startsWith: '260', country: 'Zambia', id: 238, iso: 'ZM', name: 'ZAMBIA', nicename: 'Zambia', iso3: "ZMB", numcode: 894, phonecode: 260 },
		{ mask: '+000 000 000 0000', startsWith: '263', country: 'Zimbabwe', id: 239, iso: 'ZW', name: 'ZIMBABWE', nicename: 'Zimbabwe', iso3: "ZWE", numcode: 716, phonecode: 263 },
		{ mask: '+0000000000000', startsWith: '', country: 'Default', id: 0, iso: '', name: '', nicename: '', iso3: null, numcode: null, phonecode: 0 }  // fallback mask
	];
	return phone_mask_arr;
}


function wpbc_guess_country_by_locale() {

	var locale          = navigator.language || navigator.userLanguage || '';
	var parts           = locale.split( '-' );
	var detected_locale = '';
	if ( parts.length > 1 ) {
		detected_locale = parts[1].toUpperCase();
	} else {
		var mask_by_ISO = wpbc_get_phone_mask_by_country_ISO( parts[0].toUpperCase() );
		if ( null !== mask_by_ISO ) {
			detected_locale = mask_by_ISO.iso;
		}
	}
	return detected_locale
}


function wpbc_get_phone_mask_by_country_ISO( isoCode ) {
    var maskList = wpbc_get_prepared_phones_mask();
    for (var i = 0; i < maskList.length; i++) {
        if (maskList[i].iso === isoCode) {
            return maskList[i];
        }
    }
    return null;
}