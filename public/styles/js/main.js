class Artikel {

	constructor() {

		// Typeselectors in Lists and Detail
		this.typeSelectors = document.querySelectorAll('.js-type-selector');
		this.listenToTypeSelector(this.typeSelectors);

		// Timeselector Submit on Change
		let timeframe = document.querySelector('.js-timeframe');
		if (timeframe) {
			timeframe.addEventListener('change', () => {timeframe.parentNode.submit();});
		}


		// Sort Table Stuff
		this.sortableTables = document.querySelectorAll('.js-sortable');
		if (this.sortableTables) {this.tableSort(this.sortableTables);}

	}






	listenToTypeSelector(selectElements) {
		if (!selectElements) {return;}
		let _this = this;
		Array.from(selectElements).forEach(selectBox => {
		    selectBox.addEventListener('change', (e) => {
				let id = selectBox.getAttribute('data-id');
				_this.saveType(e.target.value, id);
		    });
		});
	}

	saveType(articleType, id) {

		let data = new FormData();
		data.append('type', articleType);

		fetch('/artikel/'+id, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		})
			.then(response => {
				if(response.status === 404) {
					console.log(response.text()); return null;
				}
				return response.text();
			})
			.then(phpresponse => {
				console.log(phpresponse);
				//window.location.reload();
			})
			.catch(error => {
				console.error(`Oops: ${error}`);
			});
	}



	tableSort(sortableTables) {

		let this_ = this;

		Array.from(sortableTables).forEach(table => {

			table.addEventListener( 'mouseup', function ( e ) {

				// proceed Only for Left Click
				if (e.button != 0) {return;}

				/*
				 * sortable 1.0
				 * Copyright 2017 Jonas Earendel
				 * https://github.com/tofsjonas/sortable
				*/

			    var down_class = ' dir-d ';
			    var up_class = ' dir-u ';
			    var regex_dir = / dir-(u|d) /;
			    var regex_table = /\bsortable\b/;
			    var element = e.target;

			    function reclassify( element, dir ) {
			        element.className = element.className.replace( regex_dir, '' ) + (dir?dir:'');
			    }

			    if ( element.nodeName == 'TH' ) {

			        var table = element.offsetParent;

			        // make sure it is a sortable table
			        if ( regex_table.test( table.className ) ) {

			            var column_index;
			            var tr = element.parentNode;
			            var nodes = tr.cells;

			            // reset thead cells and get column index
			            for ( var i = 0; i < nodes.length; i++ ) {
			                if ( nodes[ i ] === element ) {
			                    column_index = i;
			                } else {
			                    reclassify( nodes[ i ] );
			                    // nodes[ i ].className = nodes[ i ].className.replace( regex_dir, '' );
			                }
			            }

			            var dir = up_class;

			            // check if we're sorting up or down, and update the css accordingly
			            if ( element.className.indexOf( up_class ) !== -1 ) {
			                dir = down_class;
			            }

			            reclassify( element, dir );

			            // extract all table rows, so the sorting can start.
			            var org_tbody = table.tBodies[ 0 ];

			            var rows = [].slice.call( org_tbody.cloneNode( true ).rows, 0 );
			            // slightly faster if cloned, noticable for huge tables.

			            var reverse = ( dir == up_class );

			            // sort them using custom built in array sort.
			            rows.sort( function ( a, b ) {

							// sorting with Dates
							if (a.cells[ column_index ].hasAttribute('data-sortdate')){
								a = a.cells[ column_index ].getAttribute('data-sortdate');
							}
							else {
								a = a.cells[ column_index ].innerText;
							}

							if (b.cells[ column_index ].hasAttribute('data-sortdate')){
								b = b.cells[ column_index ].getAttribute('data-sortdate');
							}
							else {
								b = b.cells[ column_index ].innerText;
							}

			                if ( reverse ) {
			                    var c = a;
			                    a = b;
			                    b = c;
			                }

							// Parse to Float when % detected
							if (a.match(/%/g)) {
								a = parseFloat(a);
							} else {a = a.replace('.','');}

							if (b.match(/%/g)) {
								b = parseFloat(b);
							} else {b = b.replace('.','');}

			                return isNaN( a - b ) ? a.localeCompare( b ) : a - b;
			            } );

			            // Make a clone without contents
			            var clone_tbody = org_tbody.cloneNode();

			            // Build a sorted table body and replace the old one.
			            for ( i in rows ) {
			                clone_tbody.appendChild( rows[ i ] );
			            }

			            // And finally insert the end result
			            table.replaceChild( clone_tbody, org_tbody );

						// Reassign Select Boxes
						let typesSelectBoxes = document.querySelectorAll('.js-type-selector');
						this_.listenToTypeSelector(typesSelectBoxes);

			        }

			    }

			}); // End sorttable Plugin

		}); // End For Each Array

	} // End Tablesort Method




}


//When the DOM is fully loaded - aka Document Ready
document.addEventListener("DOMContentLoaded", function(){
	const artikel = new Artikel();
});