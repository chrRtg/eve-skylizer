$(document).ready(function ()
{
	// structure tooltip (http://iamceege.github.io/tooltipster/)
	$('a.structlink').tooltipster({
		content: '',
		contentAsHTML: true,
		side: 'left',
		animation: 'fade',
		updateAnimation: 'fade',
		delay: [400,100],
		distance: 1,
		theme: 'tooltipster-borderless',

		// 'instance' is basically the tooltip. More details in the "Object-oriented Tooltipster" section.
		functionBefore: function (instance, helper) 
		{
			var $origin = $(helper.origin);
			var structid = $origin.context.dataset.structid;

			if (!structid) {
				instance.content("no struture found, maybe you want to add one?");
			} else if ($origin.data('loaded') !== true) {
				// we set a variable so the data is only loaded once via Ajax, not every time the tooltip opens
				instance.content('<img src="/img/sl_loading.gif" height="40" width="70" alt="loading..."><br>loading...');

				$.get('/vposmoon/getStructureJson/0?q=' + structid, function (data) {
					// call the 'content' method to update the content of our tooltip with the returned data.
					// note: this content update will trigger an update animation (see the updateAnimation option)
					instance.content(formatStructureTip(data.items[0]));

					// to remember that the data has been loaded
					$origin.data('loaded', true);
				});
			}
		}
	});

	function formatStructureTip(ditems) {
		 console.log(ditems);

		var markup = '<div>'+ditems.groupname+' : <strong>'+ditems.structItemname+'</strong> <span class="skylizer">'+ditems.structureName+'</span></div>' +
		'<div class="addpaddingtop10"><span class="addfontsize3">'+ditems.corporationName+'</span> <span class="secondarytext addfontsize3">['+ditems.corporationTicker+']</span><br>' +
		'Member of <strong>'+ditems.allianceName+'</strong> <span class="secondarytext">['+ditems.allianceTicker+']</span></div>' +
		'<div class="addpaddingtop10 skylizer_dim">last seen <strong>'+ditems.lastseenName+'</strong> <span class="secondarytext">'+ditems.lastseenDate+'</span><br>' +
		'scanned by <strong>'+ditems.creaName+'</strong> <span class="secondarytext">'+ditems.createDate+'</span></div>';

		return markup;
	}



	//datatable for moonTable
	var moon_table = $('#moontable').DataTable({
		fixedHeader: true,
		responsive: true
	});
	moon_table.fixedHeader.headerOffset($('#skylizer_navbar').height());

	// auto-submit checkboxes
	$("#detail_filter_composition").change(function () {
		if (this.checked) {
			buildFilterQuery({type: "detail_filter_composition", id: "1"});
		} else {
			buildFilterQuery({type: "detail_filter_composition", id: "-1"});
		}
	});

	$("#detail_filter_ore").change(function () {
		if (this.checked) {
			buildFilterQuery({type: "detail_filter_ore", id: "1"});
		} else {
			buildFilterQuery({type: "detail_filter_ore", id: "-1"});
		}
	});

	$("#filter_gooonly").change(function () {
		if (this.checked) {
			buildFilterQuery({type: "filter_gooonly", id: "1"});
		} else {
			buildFilterQuery({type: "filter_gooonly", id: "-1"});
		}
	});
	// auto-submit links
	$('a.sytemswitch').click(function () {
		buildFilterQuery({type: "selectsystem", id: $(this).data("id")});
		return false;
	});

	$('a.resetswitch').click(function () {
		buildFilterQuery({type: $(this).data("id"), id: 0});
		return false;
	});


	// activate select2 for filters
	$('#selectcomposition').select2();
	$('#selectore').select2();

	$('#selectcomposition').on('select2:select', function (e) {
		buildFilterQuery({type: "selectcomposition", id: e.params.data.id});
	});

	$('#selectore').on('select2:select', function (e) {
		var data = e.params.data;
		buildFilterQuery({type: "selectore", id: e.params.data.id});
	});


	/**
	 * Select2 typeahead to select a system or constellation
	 */
	$('#selectsystem').on('select2:select', function (e) {
		var data = e.params.data;
		// console.log(data);
		//location.href = '/vposmoon?system=' + data.id; 
		buildFilterQuery({type: "selectsystem", id: e.params.data.id});
	});

	$('#selectsystem').select2({
		ajax: {
			url: "/vposmoon/getSystemsJson",
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term
				};
			},
			processResults: function (data, params) {
				return {
					results: data.items
				};
			},
			cache: true
		},
		placeholder: 'Search for a system or constellation',
		escapeMarkup: function (markup) {
			return markup;
		},
		minimumInputLength: 2,
		templateResult: formatRepo,
		templateSelection: formatRepoSelection
	});


	/***********************************************
	 * Structure Edit Modal
	 *************************************************/
	$('#structureEditModal').on('show.bs.modal', function (e) {
		var called_by = e.relatedTarget; // calling object (the link to open the modal)
		$("#structureEditFormMoonId").val(called_by.getAttribute('data-moonid')); // insert moonIt into structure edit form modal
		$("#structureEditFormStructureId").val(called_by.getAttribute('data-structid')); // insert moonIt into structure edit form modal
		$("#structeditname").val(called_by.getAttribute('data-structgivename')); // insert player given name
		var structtype = called_by.getAttribute('data-structtype');
		$("#structedittype").val((structtype ? structtype : 0)); // set proper selection
		var corpname = called_by.getAttribute('data-scorpname');
		if (corpname) {
			$("#structeditcorp").empty();
			$("#structeditcorp").append(new Option(called_by.getAttribute('data-scorpname'), called_by.getAttribute('data-scorpid')));
		}
	});


	/*
	 * On open structure edit modal
	 * 
	 */
	$('#structureEditModal').on('shown.bs.modal', function (e) {

		// activate select2 for structure seletion
		$('#select2-sample').select2({
			dropdownParent: $('#structureEditModal')
		});

		// activate select2 for corp seletion with AJAX call for autosuggest
		$('#structeditcorp').select2({
			dropdownParent: $('#structureEditModal'),
			ajax: {
				url: "/vposmoon/getCorporationsJson",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term
					};
				},
				processResults: function (data, params) {
					return {
						results: data.items
					};
				},
				cache: true
			},
			placeholder: 'Search by name or ticker',
			allowClear: true,
			escapeMarkup: function (markup) {
				return markup;
			},
			minimumInputLength: 2,
			templateResult: formatCorpDropDown,
			templateSelection: formatCorpSelection
		});
	});


	/*
	 * close the structure edit modal
	 */
	$('#structureEditModal').on('hidden.bs.modal', function () {
		$('#select2-sample').select2('destroy');
		$('#structeditcorp').select2('destroy');
	});


	/*
	 * on structure edit form submission
	 */
	$("#structureEditForm").on('submit', function (e) {
		e.preventDefault(); // do not send the form via his action

		$.ajax({
			url: $(this).attr('action'),
			type: "post",
			data: $(this).serializeArray(),
			beforeSend: function (e) {
				$("#structureEditModal").block({message: '<h1>update structure...</h1>'});
			},
			complete: function (e) {
				location.reload(); // no complex auto update page yet
				//$('#structureEditModal').modal('hide');
				//$("#structureEditModal").unblock(); 
			},
			success: function (data, status) {
				//
			},
			error: function (xhr) {
				$.toast({
					text: "An AJAX error occured: " + xhr.status + " " + xhr.statusText,
					heading: 'ERROR',
					icon: 'error',
					hideAfter: 5000,
					position: 'top-right',
					loader: true,
					loaderBg: '#4e433c'
				});
			}
		});
	});



	/***********************************************
	 * toaster messages
	 *************************************************/

	// see http://kamranahmed.info/toast
	if (typeof sl_messages !== 'undefined' && sl_messages) {
		for (const val of Object.values(sl_messages)) {
			switch (Object.keys(val).toString()) {
				case "info":
					$.toast({
						text: Object.values(val).toString(),
						heading: '',
						icon: 'info',
						hideAfter: 1500,
						position: 'top-right',
						loader: true,
						loaderBg: '#4e433c'
					});
					break;
				case "success":
					$.toast({
						text: Object.values(val).toString(),
						heading: '',
						icon: 'success',
						hideAfter: 1500,
						position: 'top-right',
						loader: true,
						loaderBg: '#4e433c'
					});
					break;
				case 'warning':
					$.toast({
						text: Object.values(val).toString(),
						heading: '',
						icon: 'warning',
						hideAfter: 2500,
						position: 'top-right',
						loader: true,
						loaderBg: '#4e433c'
					});
					break;
				case 'error':
					$.toast({
						text: Object.values(val).toString(),
						heading: 'ERROR',
						icon: 'error',
						hideAfter: 5000,
						position: 'top-right',
						loader: true,
						loaderBg: '#4e433c'
					});
					break;
			}
		}
	}

});

/*
 * Format Select2 dropdown selector element for Corporation Selector
 * 
 * @param {type} repo
 * @return {String}
 */
function formatCorpDropDown(repo) {

	if (repo.loading) {
		return repo.text;
	}

	var markup = "<div class='select2-result-repository clearfix'>" +
			"<div class='select2-result-repository__title'>" + repo.corporationName + " [" + repo.ticker + "]</div>";
	if (repo.allianceName == null) {
		markup += "<div class='select2-result-repository__description'>not affilated / no alliance</div>";
	} else {
		markup += "<div class='select2-result-repository__description'>Member of " + repo.allianceName + "</div>";
	}


	markup += "</div>";

	return markup;
}

/*
 * Format Select2 dropdown selected element for Corporation Selector
 * 
 * @param {type} repo
 * @return {String}
 */
function formatCorpSelection(repo) {

	if (repo.corporationName == null) {
		return repo.text;
	}

	return repo.corporationName + '  (' + repo.ticker + ')';
}



/**
 * Format select2 drop down elements for ssystem or region selector
 * 
 * @param {array} repo
 * @return {String}
 */
function formatRepo(repo) {

	if (repo.loading) {
		return repo.text;
	}

	var markup = "<div class='select2-result-repository clearfix'>" +
			"<div class='select2-result-repository__title'>" + repo.itemname + "</div>";
	if (repo.constellation == null) {
		markup += "<div class='select2-result-repository__description'>Constellation in " + repo.region + "</div>";
	} else {
		markup += "<div class='select2-result-repository__description'>" + repo.constellation + " / " + repo.region + "</div>";
	}


	markup += "</div>";

	return markup;
}

/**
 * Format select2 selected value for ssystem or region selector
 * 
 * @param {array} repo
 * @return {String}
 */
function formatRepoSelection(repo) {
	if (repo.itemname == null) {
		return repo.text;
	}

	if (repo.constellation == null) {
		return repo.itemname + ' (Constellation in ' + repo.region + ')';
	} else {
		return repo.itemname + '  (' + repo.region + ')';
	}
}

function setLocationFilter(loc_id) {
	buildFilterQuery({type: "selectsystem", id: loc_id});
}

/**
 * Takes the filter params as well a request. 
 * 
 * Out of all previous filters as well the current request this function builds a 
 * filtered query and send him to the server via location.href
 * 
 * 
 * @param {type} filter_param
 * @return {Boolean}
 */
function buildFilterQuery(filter_param)
{
	var jdata = null;
	var url_param = {};
	var url = '';

	$.blockUI({message: "<h1 class=\"block-overlay\">update the page...</h1>"});

	if (typeof filters_json === 'undefined' || !filters_json) {
		console.debug('no filter param given');
	}

	if (typeof filters_json !== 'undefined' && filters_json) {
		jdata = JSON.parse(filters_json);
		url_param['system'] = (jdata['system'] ? jdata['system'] : null);
		url_param['ore'] = (jdata['ore'] ? jdata['ore'] : null);
		url_param['composition'] = (jdata['composition'] ? jdata['composition'] : null);
	}

	if (filter_param) {
		if (filter_param.type === 'selectsystem') {
			url_param['system'] = filter_param.id;
		}
		if (filter_param.type === 'selectcomposition') {
			url_param['composition'] = filter_param.id;
		}
		if (filter_param.type === 'selectore') {
			url_param['ore'] = filter_param.id;
		}
		if (filter_param.type === 'detail_filter_composition') {
			url_param['detail_filter_composition'] = filter_param.id;
		}
		if (filter_param.type === 'detail_filter_ore') {
			url_param['detail_filter_ore'] = filter_param.id;
		}
		if (filter_param.type === 'filter_gooonly') {
			url_param['filter_gooonly'] = filter_param.id;
		}
	}

	if (url_param) {
		for (var key in url_param) {
			if (url_param[key] != null && url_param[key] != 'null') {
				url += (url ? '&' : '') + key + '=' + url_param[key];
			}
		}
		//console.log(url);
		if (url && url != '') {
			console.debug('gogo: ' + url);
			location.href = '/vposmoon?' + url;
			return false;
		}
		//console.log('oerks');
	}
	// console.log('MEGA oerks');
	//location.href = '/vposmoon';
	return false;
}