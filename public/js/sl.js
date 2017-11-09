$(document).ready(function ()
{
	
	$('a.sytemswitch').click(function() {
		buildFilterQuery({type:"selectsystem", id:$(this).data("id")});
		return false;
	});
	
	$('a.resetswitch').click(function() {
		buildFilterQuery({type:$(this).data("id"), id:0});
		return false;
	});
	
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

	function buildFilterQuery(filter_param) 
	{
		var jdata = null;
		var url_param = {};
		var url = '';
		
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
				console.log('gogo: ' +  url);
				location.href = '/vposmoon?' + url;
				return false;
			}
			console.log('oerks');
		}
		console.log('MEGA oerks');
		//location.href = '/vposmoon';
		return false;
	}


	//datatable for moonTable
	$('#moontable').DataTable();

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