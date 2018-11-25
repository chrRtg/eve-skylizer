$(document).ready(function () {
    if (typeof current_route === 'undefined') {
        current_route = 'vposmoon';
    }

    // structure tooltip (http://iamceege.github.io/tooltipster/)
    $('a.structlink').tooltipster({
        content: '',
        contentAsHTML: true,
        side: 'left',
        animation: 'fade',
        updateAnimation: 'fade',
        delay: [400, 100],
        distance: 1,
        theme: 'tooltipster-borderless',

        // 'instance' is basically the tooltip. More details in the "Object-oriented Tooltipster" section.
        functionBefore: function (instance, helper) {
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
        // console.log(ditems);

        var markup = '<div>' + ditems.groupname + ' : <strong>' + ditems.structItemname + '</strong> <span class="skylizer">' + ditems.structureName + '</span></div>';
        if (ditems.corporationName) {
            markup += '<div class="addpaddingtop10"><span class="addfontsize3">' + ditems.corporationName + '</span> <span class="secondarytext addfontsize3">[' + ditems.corporationTicker + ']</span><br>';
        }
        if (ditems.allianceName) {
            markup += 'Member of <strong>' + ditems.allianceName + '</strong> <span class="secondarytext">[' + ditems.allianceTicker + ']</span></div>';
        }
        markup += '<div class="addpaddingtop10 skylizer_dim">last seen <strong>' + ditems.lastseenName + '</strong> <span class="secondarytext">' + ditems.lastseenDate + '</span><br>';
        markup += 'scanned by <strong>' + ditems.creaName + '</strong> <span class="secondarytext">' + ditems.createDate + '</span></div>';

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
            buildFilterQuery({
                type: "detail_filter_composition",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "detail_filter_composition",
                id: "-1"
            });
        }
    });

    $("#detail_filter_ore").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "detail_filter_ore",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "detail_filter_ore",
                id: "-1"
            });
        }
    });

    $("#filter_gooonly").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "filter_gooonly",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "filter_gooonly",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_structures").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_structures",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_structures",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_gasore").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_gasore",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_gasore",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_exploration").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_exploration",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_exploration",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_combat").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_combat",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_combat",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_wormhole").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_wormhole",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_wormhole",
                id: "-1"
            });
        }
    });

    $("#vpos_filter_faction").change(function () {
        if (this.checked) {
            buildFilterQuery({
                type: "vpos_filter_faction",
                id: "1"
            });
        } else {
            buildFilterQuery({
                type: "vpos_filter_faction",
                id: "-1"
            });
        }
    });

    // auto-submit links
    $('a.sytemswitch').click(function () {
        buildFilterQuery({
            type: "system",
            id: $(this).data("id")
        });
        return false;
    });

    $('a.resetswitch').click(function () {
        buildFilterQuery({
            type: $(this).data("id"),
            id: 0
        });
        return false;
    });

    $('#vpos_showall').click(function () {
        buildFilterQuery([{
            type: "vpos_filter_structures",
            id: "1"
        },
        {
            type: "vpos_filter_gasore",
            id: "1"
        },
        {
            type: "vpos_filter_wormhole",
            id: "1"
        },
        {
            type: "vpos_filter_exploration",
            id: "1"
        },
        {
            type: "vpos_filter_faction",
            id: "1"
        },
        {
            type: "vpos_filter_combat",
            id: "1"
        }
        ]);
        return false;
    });

    $('#vpos_shownone').click(function () {
        buildFilterQuery([{
            type: "vpos_filter_structures",
            id: "-1"
        },
        {
            type: "vpos_filter_gasore",
            id: "-1"
        },
        {
            type: "vpos_filter_wormhole",
            id: "-1"
        },
        {
            type: "vpos_filter_exploration",
            id: "-1"
        },
        {
            type: "vpos_filter_faction",
            id: "-1"
        },
        {
            type: "vpos_filter_combat",
            id: "-1"
        }
        ]);
        return false;
    });


    // activate select2 for filters
    $('#selectcomposition').select2();
    $('#selectore').select2();

    $('#selectcomposition').on('select2:select', function (e) {
        buildFilterQuery({
            type: "composition",
            id: e.params.data.id
        });
    });

    $('#selectore').on('select2:select', function (e) {
        var data = e.params.data;
        buildFilterQuery({
            type: "ore",
            id: e.params.data.id
        });
    });


    /**
     * Select2 typeahead to select a system or constellation
     */
    $('#selectsystem').on('select2:select', function (e) {
        var data = e.params.data;
        // console.log(data);
        //location.href = '/'+current_route+'?system=' + data.id;
        buildFilterQuery({
            type: "system",
            id: e.params.data.id
        });
    });

    $('#selectsystem').select2({
        ajax: {
            url: '/vposmoon/getSystemsJson',
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


    /*
     * Typeahead fetcher for System selector
    */
   systemtypedata = [
    { id: "-7", itemid: "-7", itemname: "High", constellation: "", constellationid: "", region: "", regionid: "", classidH: null, classidL: "7" },
    { id: "-8", itemid: "-8", itemname: "Low", constellation: "", constellationid: "", region: "", regionid: "", classidH: null, classidL: "8" },
    { id: "-9", itemid: "-9", itemname: "Null", constellation: "", constellationid: "", region: "", regionid: "", classidH: null, classidL: "9" },
    { id: "-10", itemid: "-10", itemname: "Wormhole", constellation: "", constellationid: "", region: "", regionid: "", classidH: null, classidL: "10" }];

    var fetchSystemsSuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace(datum.value);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '/vposmoon/getSystemsJson?q=%QUERY',
            wildcard: '%QUERY',
            transform: function (systems) {
                return $.map(systems.items, function (system) {
                    return (system);
                });
            }
        }
    });

    function dispatchSystemsSuggestion(q, sync, async) {
        if (q.length <= 1) {
            sync(systemtypedata);
        }
        else {
            fetchSystemsSuggestion.search(q, sync, async);
        }
    }

    // Typeahead for system selector (Wormhole connections)
    $('[data-action="settargetsystem"]').typeahead({
        //hint: true,
        highlight: true,
        minLength: 0,
    }, {
            name: 'itemname',
            display: 'itemname',
            source: dispatchSystemsSuggestion,
            templates: {
                suggestion: function (data) {
                    return '<p><strong>' + data.itemname + '</strong> <span class="secondarytext">(' + formatClassid(data.classidH, data.classidL) + ')</span><br>' + data.region + '</p>';
                }
            }
        }).on('typeahead:close', function (ev, suggestion) {
            // on typeahead close -> no selection done -> empty value
            ev.currentTarget.value = '';
        }).on('typeahead:selected', function (ev, suggestion) {
            // on selection create the connection
            ev.stopPropagation();

            var structid = ev.currentTarget.attributes.getNamedItem('data-pk').value;
            var targetid = suggestion.id;

            if (!structid || !targetid) {
                return null;
            }

            $.blockUI({
                message: "<h1 class=\"block-overlay\">store connection and update page...</h1>"
            });
            location.href = '/vpos/addSystemConnection?structid=' + structid + '&targetid=' + targetid;
            return false;
        });


    /*
     * Values for classid:
     *    1 - 6 are for w-space, with 1 being easy / unrewarding and 6 being hard / lucrative.
     *    7 is highsec,
     *    8 is lowsec, and
     *    9 is nullsec.
     */
    function formatClassid(classidH, classidL) {
        if (!classidL || classidL === 0) {
            return '';
        }

        var val = 0;
        if (classidH && classidH !== 0) {
            val = parseInt(classidH);
        } else {
            val = parseInt(classidL);
        }

        switch (val) {
            case 7:
                return 'high';
            case 8:
                return 'low';
            case 9:
                return '0.0';
            case 10:
                return 'WH';
            default:
                return 'C' + val;
        }
    }

    /***********************************************
     * Structure Edit Modal
     * step #1 if modal opens
     *************************************************/
    $('#structureEditModal').on('show.bs.modal', function (e) {
        var called_by = e.relatedTarget; // calling object (the link to open the modal)
        $("#structureEditFormMoonId").val(called_by.getAttribute('data-moonid')); // insert moonIt into structure edit form modal
        $("#structureEditFormStructureId").val(called_by.getAttribute('data-structid')); // insert moonIt into structure edit form modal
        $("#structeditname").val(called_by.getAttribute('data-structgivename')); // insert player given name
        var structtype = called_by.getAttribute('data-structtype');
        if (structtype) {
            $("#structedittype").val(structtype); // set proper selection
            $("#structedittype").attr('disabled', true);
        }
        var corpname = called_by.getAttribute('data-scorpname');
        if (corpname) {
            $("#structeditcorp").empty();
            $("#structeditcorp").append(new Option(called_by.getAttribute('data-scorpname'), called_by.getAttribute('data-scorpid')));
        }
    });


    /*
     * On open structure edit modal
     * step #2 when modal has been initialized (opened)
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
                url: '/vposmoon/getCorporationsJson',
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
     * Delete a structure
     */
    $('#structureDeleteFormSubmit').click(function () {

        delid = $('#structureEditFormStructureId').val();

        if (!delid) {
            return;
        }

        $.ajax({
            url: '/vposmoon/deleteStructureJson?id=' + delid,
            dataType: 'json',
            beforeSend: function (e) {
                $("#structureEditModal").block({
                    message: '<h1>delete this structure...</h1>'
                });
            },
            complete: function (e) {
                location.reload(); // no complex auto update page yet
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
                $("#structureEditModal").block({
                    message: '<h1>update structure...</h1>'
                });
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
    buildFilterQuery({
        type: "system",
        id: loc_id
    });
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
function buildFilterQuery(filter_param) {
    var jdata = null;
    var url_param = {};
    var url = '';

    $.blockUI({
        message: "<h1 class=\"block-overlay\">update the page...</h1>"
    });

    if (typeof filters_json === 'undefined' || !filters_json) {
        console.debug('no filter param given');
    }

    if (typeof filters_json !== 'undefined' && filters_json) {
        jdata = JSON.parse(filters_json);
        url_param['system'] = (jdata['system'] ? jdata['system'] : null);
        url_param['ore'] = (jdata['ore'] ? jdata['ore'] : null);
        url_param['composition'] = (jdata['composition'] ? jdata['composition'] : null);
    }


    // add all filter parameters to URL Parameters
    if (isArray(filter_param)) {
        for (var fk in filter_param) {
            url_param = addUrlParam(url_param, filter_param[fk]);
        }
    } else {
        url_param = addUrlParam(url_param, filter_param);
    }


    if (url_param) {
        for (var key in url_param) {
            if (url_param[key] != null && url_param[key] != 'null') {
                url += (url ? '&' : '') + key + '=' + url_param[key];
            }
        }
        //console.log(url);
        if (url && url != '') {
            //console.debug('gogo: ' + url);
            //console.debug('route: ' + current_route);
            location.href = '/' + current_route + '?' + url;
            return false;
        }
        //console.log('oerks');
    }
    // console.log('MEGA oerks - well stay - do nothing');
    return false;
}

function addUrlParam(url_param, filter_param) {

    if (typeof filter_param.type !== 'undefined' && typeof filter_param.id !== 'undefined') {
        url_param[filter_param.type] = filter_param.id;
    }

    return url_param;
}

function isArray(obj) {
    return !!obj && obj.constructor === Array;
}