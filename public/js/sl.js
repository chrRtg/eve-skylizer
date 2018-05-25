$(document).ready(function ()
{
	//datatable for moonTable
	$('#moontable').DataTable();
	//$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);


	$('#structureEditModal').on('show.bs.modal', function (e) {
		var called_by = e.relatedTarget; // calling object (the link to open the modal)
		$("#structureEditFormMoonId").val( called_by.getAttribute('data-moonid') ); // insert moonIt into structure edit form modal
		$("#structureEditFormStructureId").val( called_by.getAttribute('data-structid') ); // insert moonIt into structure edit form modal
		$("#structeditname").val( called_by.getAttribute('data-structgivename') ); // insert player given name
		var structtype = called_by.getAttribute('data-structtype');
		$("#structedittype").val( (structtype ? structtype : 0 ) ); // set proper selection
	})
	
	// activate structure edit form submit button
	$("#structureEditFormSubmit").on('click', function () {
		
		$.ajax({
			url: $(this.form).attr('action'),
			type: "post",
			data: $(this.form).serializeArray(),
			beforeSend: function (e) { $("#structureEditModal").block({ message: '<h1>update structure...</h1>' }); }, 
			complete: function (e) { 
				location.reload(); // no complex auto update page yet
				//$('#structureEditModal').modal('hide');
				//$("#structureEditModal").unblock(); 
			}, 
			success: function (data, status) {
				//
			},
			error: function(xhr) {
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



	// auto-submit checkboxes
	$("#detail_filter_composition").change(function() {
		if (this.checked) {
			buildFilterQuery({type:"detail_filter_composition", id:"1"});
		} else {
			buildFilterQuery({type:"detail_filter_composition", id:"-1"});
		}
	});	

	$("#detail_filter_ore").change(function() {
		if (this.checked) {
			buildFilterQuery({type:"detail_filter_ore", id:"1"});
		} else {
			buildFilterQuery({type:"detail_filter_ore", id:"-1"});
		}
	});	

	// auto-submit links
	$('a.sytemswitch').click(function() {
		buildFilterQuery({type:"selectsystem", id:$(this).data("id")});
		return false;
	});
	
	$('a.resetswitch').click(function() {
		buildFilterQuery({type:$(this).data("id"), id:0});
		return false;
	});
	
	
	// activate select2 for filters
	$('#selectcomposition').select2();
	$('#selectore').select2();

	$('#selectcomposition').on('select2:select', function (e) {
		buildFilterQuery({type:"selectcomposition", id:e.params.data.id});
	});

	$('#selectore').on('select2:select', function (e) {
		var data = e.params.data;
		buildFilterQuery({type:"selectore", id:e.params.data.id});
	});


	/**
	 * Select2 typeahead to select a system or constellation
	 */
	$('#selectsystem').on('select2:select', function (e) {
		var data = e.params.data;
		// console.log(data);
		//location.href = '/vposmoon?system=' + data.id; 
		buildFilterQuery({type:"selectsystem", id:e.params.data.id});
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
					results: data.items,
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

	// show toaster messages
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




/**
 * Format select2 drop down elements
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
	if(repo.constellation == null) {
		markup += "<div class='select2-result-repository__description'>Constellation in " + repo.region + "</div>";
	} else {
		markup += "<div class='select2-result-repository__description'>" + repo.constellation + " / " + repo.region + "</div>";
	}


	markup += "</div>";

	return markup;
}

/**
 * Format select2 selected value
 * 
 * @param {array} repo
 * @return {String}
 */
function formatRepoSelection(repo) {
	if(repo.itemname == null) {
		return repo.text;
	}

	if(repo.constellation == null) {
		return repo.itemname + ' (Constellation in '+repo.region+')';
	} else {
		return repo.itemname + '  ('+repo.region+')';
	}
}

function setLocationFilter(loc_id) {
	buildFilterQuery({type:"selectsystem", id:loc_id});
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

	$.blockUI({ message: "<h1 class=\"block-overlay\">update the page...</h1>"}); 

	if (typeof filters_json === 'undefined' || !filters_json) {
		console.debug('no filter param given');
	}

	if (typeof filters_json !== 'undefined' && filters_json) {
		jdata = JSON.parse(filters_json);
		url_param['system'] = (jdata['system'] ? jdata['system'] : null);
		url_param['ore'] = (jdata['ore'] ? jdata['ore'] : null);
		url_param['composition'] = (jdata['composition'] ? jdata['composition'] : null);
	}

	if(filter_param) {
		if(filter_param.type === 'selectsystem') {
			url_param['system'] = filter_param.id;
		}
		if(filter_param.type === 'selectcomposition') {
			url_param['composition'] = filter_param.id;
		}
		if(filter_param.type === 'selectore') {
			url_param['ore'] = filter_param.id;
		}
		if(filter_param.type === 'detail_filter_composition') {
			url_param['detail_filter_composition'] = filter_param.id;
		}
		if(filter_param.type === 'detail_filter_ore') {
			url_param['detail_filter_ore'] = filter_param.id;
		}
	}

	if(url_param) {
		for (var key in url_param) {
//				if(url_param[key] && url_param[key] != 'null') {
			if(url_param[key] != null && url_param[key] != 'null') {
				url += (url ? '&' : '') + key + '=' + url_param[key];
			}
		}
		console.log(url);
		if(url && url != '') {
			console.debug('gogo: ' +  url);
			location.href = '/vposmoon?' + url;
			return false;
		}
		//console.log('oerks');
	}
	// console.log('MEGA oerks');
	//location.href = '/vposmoon';
	return false;
}