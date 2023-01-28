class Imports {

	constructor() {
		this.ignoreCancelled = false;
	}

	run (event) {

		event.preventDefault();
		let from = event.target.querySelector('input[name="from"]').value || new Date();
		let to = event.target.querySelector('input[name="to"]').value || new Date();
		this.ignoreCancelled = event.target.querySelector('input[name="ignore-cancelled"]').checked;
		let dates = this.date_span(from, to);

		console.log(dates);

		this.prepare(dates);
		this.import(dates).then(response => {this.aftercare()});

	}


	prepare(dates) {

		this.output = document.querySelector('#output') || null;
		this.output.innerHTML = '';
		this.output.style.border = '1px solid black';
		this.output.style.padding = '1em';
		this.output.style.display = 'inline-block';

		this.loader = document.createElement('progress');
		this.loader.max = 100;
		this.loader.style.display = 'block';

		/*
		this.loader.style.background = 'black';
		this.loader.style.height = '.3em';
		this.loader.style.display = 'block';
		this.loader.style.width = '10%';
		*/

		this.output.appendChild(this.loader);

		this.importedDays = 0;
		this.importedDuration = 0;
		this.daysToImport = dates.length;
		this.output.appendChild(document.createTextNode("Zu Importierende Tage: "+this.daysToImport))
	}

	aftercare() {

		let seconds = this.importedDuration / 1000 + 's';

		this.output.appendChild(document.createTextNode("Gesamtdauer: " + seconds))

	}


	to_iso(date) {
		return date.toISOString().split('T')[0];
	}

	date_span(startDate, endDate) {

		if (typeof startDate.getMonth !== 'function') {startDate = new Date(startDate);}
		if (typeof endDate.getMonth !== 'function') {endDate = new Date(endDate);}

		if (startDate > endDate) {
			let temp = startDate;
			startDate = endDate;
			endDate = temp;
		}

		let dates = []
		//to avoid modifying the original date
		const theDate = new Date(startDate)
		while (theDate < endDate) {
			dates = [...dates, this.to_iso(new Date(theDate))]
			theDate.setDate(theDate.getDate() + 1)
		}
		dates = [...dates, this.to_iso(endDate)]
		return dates
	}


	log(orders, date, duration) {

		let container = document.createElement('div');

		duration = duration / 1000;

		let text = document.createElement('p');
		text.innerText = date + ' | erneuerte Käufe: ' + orders.length + ' | Importdauer: ' + duration + 's';

		container.appendChild(text);
		this.output.appendChild(container);

	}

	async import(dates) {

		for(const date of dates) {

			let ignoreOption = '';
			if (this.ignoreCancelled) {ignoreOption = '?ignorecancelled';}

			let start = new Date().getTime();

			await fetch('/orders/import/' + date + ignoreOption, {method: 'GET', credentials: 'same-origin'})
			.then(response => {
				if(response.status === 404) {console.log(response.text()); return null;}
				return response.json();
			})
			.then(orders => {
				let end = new Date().getTime();
				let duration = end - start;
				this.importedDays++;
				this.importedDuration = this.importedDuration + duration
				this.loader.value = Math.round((this.importedDays / this.daysToImport * 100));
				this.log(orders, date, duration);
			})
			.catch(error => {
				console.error(`Oops: ${error}`);
			});
		}

	};




}






class Artikel {

	constructor() {

		// Typeselectors in Lists and Detail
		this.typeSelectors = document.querySelectorAll('.js-type-selector');
		this.listenToTypeSelector(this.typeSelectors);

		// Audienceselectors
		this.audienceSelectors = document.querySelectorAll('.js-audience-selector');
		this.listenToAudienceSelector(this.audienceSelectors);

		// Tagselectors
		this.tagSelectors = document.querySelectorAll('.js-tag-selector');
		this.listenToTagSelector(this.tagSelectors);

		let tableCollapseSelectors = document.querySelectorAll('.js-collapse-table-btn');
		if (tableCollapseSelectors) {

			Array.from(tableCollapseSelectors).forEach(button => {
				button.addEventListener('click', (e) => {
					let table = document.querySelector('.js-collapse-table');
					table.classList.toggle('collapsed');
				});
			});
		}

		// Timeselector Submit on Change
		let timeframe = document.querySelector('.js-timeframe');
		if (timeframe) {
			timeframe.addEventListener('change', () => {timeframe.parentNode.submit();});
		}

		// Portalselector
		let portalSelector = document.querySelector('.js-portal-select');
		if (portalSelector) {
			portalSelector.addEventListener('change', (e) => {
				let portal = e.currentTarget.value;
				let subdomain = 'reports';

				let from = portalSelector.getAttribute('data-from');
				let to = portalSelector.getAttribute('data-to');

				if (portal == 'MOZ') {subdomain = 'reports-moz';}
				if (portal == 'SWP') {subdomain = 'reports-swp';}
				if (portal == 'LR') {subdomain = 'reports';}

				let oldPath = window.location.href;
				let newPath = oldPath.replace(/^[^.]*/, subdomain)
				let page = new URL(oldPath);

				window.location = `https://${subdomain}.lr-digital.de/switch-portal?page=${page.pathname}&from=${from}&to=${to}&get=${page.search}`;

			});
		}

		// Icon Load Animation
		this.loadAnimation();

		// Sort Table Stuff
		this.sortableTables = document.querySelectorAll('.js-sortable');
		if (this.sortableTables) {this.tableSort(this.sortableTables);}

	}

	loadAnimation() {

		let icon = document.querySelector('.icon-analytics');
		if (!icon) {return;}
		icon.parentNode.addEventListener('click', (e) => {
			e.preventDefault();
			icon.src = '/styles/img/load.svg';
			location.href = icon.parentNode.href;
		})
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


	listenToTagSelector(selectElements) {
		if (!selectElements) {return;}
		let _this = this;
		Array.from(selectElements).forEach(selectBox => {
		    selectBox.addEventListener('change', (e) => {
				let id = selectBox.getAttribute('data-id');
				_this.saveTag(e.target.value, id);
		    });
		});
	}

	saveTag(articleType, id) {

		let data = new FormData();
		data.append('tag', articleType);

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

	listenToAudienceSelector(selectElements) {
		if (!selectElements) {return;}
		let _this = this;
		Array.from(selectElements).forEach(selectBox => {
		    selectBox.addEventListener('change', (e) => {
				let id = selectBox.getAttribute('data-id');
				_this.saveAudience(e.target.value, id);
		    });
		});
	}

	saveAudience(articleType, id) {

		let data = new FormData();
		data.append('audience', articleType);

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
	window.imports = new Imports();
});
