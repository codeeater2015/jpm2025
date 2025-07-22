var JtrTaggingDatatable = React.createClass({

    getInitialState: function () {
        return {
            showCreateModal: false,
            target: null,
            typingTimer: null,
            doneTypingInterval: 1500,
            user: null,
            filters: {
                electId: null,
                provinceCode: null,
                proId: null
            }
        }
    },

    componentDidMount: function () {
        this.loadUser(window.userId);
        this.initSelect2();
    },

    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.reinitSelect2);
        });
    },

    initSelect2: function () {
        var self = this;

        $("#handler_component #election_select2").select2({
            casesentitive: false,
            placeholder: "Select Election...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_elections'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.elect_id, text: item.elect_name };
                        })
                    };
                },
            }
        });

        $("#handler_component #project_select2").select2({
            casesentitive: false,
            placeholder: "Select Project...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_projects'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.pro_id, text: item.pro_name };
                        })
                    };
                },
            }
        });

        $("#handler_component #province_select2").select2({
            casesentitive: false,
            placeholder: "Enter Province...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_province_strict'),
                data: function (params) {
                    return {
                        searchText: params.term
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.province_code, text: item.name };
                        })
                    };
                },
            }
        });

        $("#handler_component #election_select2").on("change", function () {
            var filters = self.state.filters;
            filters.electId = $(this).val();

            self.setState({ filters: filters }, self.reload);
        });

        $("#handler_component #project_select2").on("change", function () {
            var filters = self.state.filters;
            filters.proId = $(this).val();
            self.setState({ filters: filters }, self.reload);
        });

        $("#handler_component #province_select2").on("change", function () {
            var filters = self.state.filters;
            filters.provinceCode = $(this).val();
            self.setState({ filters: filters }, self.reload);
        });

    },

    reinitSelect2: function () {
        var self = this;

        if (!self.isEmpty(self.state.user.project)) {
            var provinceCode = self.state.user.project.provinceCode;

            self.requestProvince = $.ajax({
                url: Routing.generate("ajax_get_province", { provinceCode: provinceCode }),
                type: "GET"
            }).done(function (res) {
                $("#handler_component #province_select2").empty()
                    .append($("<option/>")
                        .val(res.province_code)
                        .text(res.name))
                    .trigger("change");
            });

            self.requestProject = $.ajax({
                url: Routing.generate("ajax_get_project", { proId: self.state.user.project.proId }),
                type: "GET"
            }).done(function (res) {
                $("#handler_component #project_select2").empty()
                    .append($("<option/>")
                        .val(res.proId)
                        .text(res.proName))
                    .trigger("change");

                self.initDatatable();
            });
        }

        self.requestActiveElection = $.ajax({
            url: Routing.generate("ajax_get_active_election"),
            type: "GET"
        }).done(function (res) {
            $("#handler_component #election_select2").empty()
                .append($("<option/>")
                    .val(res.electId)
                    .text(res.electName))
                .trigger("change");
        });

        if (!self.state.user.isAdmin) {
            $("#handler_component #election_select2").attr('disabled', 'disabled');
            $("#handler_component #province_select2").attr('disabled', 'disabled');
            $("#handler_component #project_select2").attr('disabled', 'disabled');
        }
    },

    initDatatable: function () {
        var self = this;
        var grid = new Datatable();

        var jtr_tagging_datatable = $("#jtr_tagging_datatable");
        var grid_project_recruitment = new Datatable();
        var url = Routing.generate("ajax_jtr_tagging_datatable", {}, true);

        grid_project_recruitment.init({
            src: jtr_tagging_datatable,
            loadingMessage: 'Loading...',
            "dataTable": {
                "bState": true,
                "autoWidth": true,
                "deferRender": true,
                "ajax": {
                    "url": url,
                    "type": 'GET',
                    "data": function (d) {
                        d.electId = $('#handler_component #election_select2').val();
                        d.provinceCode = $('#handler_component #province_select2').val();
                        d.proId = $('#handler_component #project_select2').val();
                        d.voterName = $('#jtr_tagging_datatable input[name="voter_name"]').val();
                        d.municipalityName = $('#jtr_tagging_datatable input[name="municipality_name"]').val();
                        d.barangayName = $('#jtr_tagging_datatable input[name="barangay_name"]').val();
                        d.voterGroup = $('#jtr_tagging_datatable input[name="voter_group"]').val();
                        d.precinctNo = $('#jtr_tagging_datatable input[name="precinct_no"]').val();


                        d.isAdditional = $('#jtr_tagging_datatable select[name="is1CheckFilter"]').val();
                        d.isJtrMember = $('#jtr_tagging_datatable select[name="is2CheckFilter"]').val();
                    }
                },
                "columnDefs": [{
                    'orderable': false,
                    'targets': [0, 2, 3, 4, 5, 6, 7, 8, 9]
                }, {
                    'className': 'align-center',
                    'targets': [0, 3]
                }],
                "order": [
                    [0, "desc"]
                ],
                "columns": [
                    {
                        "data": null,
                        "className": "text-center",
                        "width": 30,
                        "render": function (data, type, full, meta) {
                            return meta.settings._iDisplayStart + meta.row + 1;
                        }
                    },
                    {
                        "data": "voter_name",
                        "width": 250,
                        "render": function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        "data": "is_additional",
                        "className": "text-center",
                        "width": 30,
                        "render": function (data, type, row) {
                            return '<label class="mt-checkbox status-checkbox"><input type="checkbox" name="isAdditional" ' + ((parseInt(data) == 1) ? ' checked="checked" ' : '') + ' value="' + row.pro_voter_id + '"></input><span></span></label>';
                        }
                    },
                    {
                        "data": "is_jtr_member",
                        "className": "text-center",
                        "width": 30,
                        "render": function (data, type, row) {
                            return '<label class="mt-checkbox status-checkbox"><input type="checkbox" name="isJtrMember" ' + ((parseInt(data) == 1) ? ' checked="checked" ' : '') + ' value="' + row.pro_voter_id + '"></input><span></span></label>';
                        }
                    },
                    { "data": "precinct_no", "className": "text-center", width: 100 },
                    {
                        "data": "has_new_photo",
                        "className": "text-center",
                        "width": 50,
                        "render" : function(data){
                            return Number.parseInt(data) == 1 ? "YES" : "NO"
                        }
                    },
                    {
                        "data": "has_new_id",
                        "className": "text-center",
                        "width": 50,
                        "render" : function(data){
                            return Number.parseInt(data) == 1 ? "YES" : "NO"
                        }
                    },
                    { "data": "municipality_name", width: 150, "className": 'text-center' },
                    { "data": "barangay_name", width: 150, "className": 'text-center' },
                    { "data": "voter_group", "className": "text-center", width: 100 },
                ],
            }
        });

        jtr_tagging_datatable.on('click', '.status-checkbox', function (e) {
            var proVoterId = e.target.value;
            var checked = e.target.checked;
            var fieldName = e.target.name;
            var newValue = checked ? 1 : 0;

            if (proVoterId != null && checked != null) {
                self.patchStatus(proVoterId, fieldName, newValue);
            }
        });

        jtr_tagging_datatable.on('click', '.recruits-button', function () {
            var data = grid_project_recruitment.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showRecruitsModal: true, target: data.rec_id });
        });

        jtr_tagging_datatable.on('click', '.delete-button', function () {
            var data = grid_project_recruitment.getDataTable().row($(this).parents('tr')).data();
            self.delete(data.rec_id);
        });

        self.grid = grid_project_recruitment;
    },


    closeCreateModal: function () {
        this.setState({ showCreateModal: false, target: null });
    },

    closeRecruitsModal: function () {
        this.setState({ showRecruitsModal: false, target: null });
    },

    openCreateModal: function () {
        this.setState({ showCreateModal: true });
    },


    patchStatus: function (proVoterId, fieldName, value) {
        var self = this;
        var data = {};

        data[fieldName] = value;
        self.requestToggleRequirement = $.ajax({
            url: Routing.generate("ajax_patch_project_voter_jtr_status", { proVoterId: proVoterId }),
            type: "PATCH",
            data: (data)
        }).done(function (res) {
            self.reload();
        });
    },

    delete: function (recId) {
        var self = this;

        if (confirm("Are you sure you want to delete this record ?")) {
            self.requestDelete = $.ajax({
                url: Routing.generate("ajax_delete_project_recruitment_header", { recId: recId }),
                type: "DELETE"
            }).done(function (res) {
                self.reload();
            });
        }
    },

    handleFilterChange: function () {
        var self = this;
        clearTimeout(this.state.typingTimer);
        this.state.typingTimer = setTimeout(function () {
            self.reload();
        }, this.state.doneTypingInterval);
    },

    reload: function () {
        if (this.grid != null) {
            this.grid.getDataTable().ajax.reload();
        }
    },

    isEmpty: function (value) {
        return value == null || value == "" || value == "undefined" || value <= 0;
    },

    render: function () {
        return (
            <div>
                {
                    this.state.showCreateModal &&
                    <PulahanCreateModal
                        proId={this.state.filters.proId}
                        electId={this.state.filters.electId}
                        provinceCode={this.state.filters.provinceCode}
                        show={this.state.showCreateModal}
                        notify={this.props.notify}
                        reload={this.reload}
                        onHide={this.closeCreateModal}
                    />
                }

                <div className="row" id="handler_component">
                    <div className="col-md-5">
                        <button type="button" className="btn btn-primary" onClick={this.openCreateModal}>Add Voter</button>
                    </div>

                    <div className="col-md-7">
                        <form onSubmit={this.onApplyCode}>
                            <div className="col-md-3 col-md-offset-1">
                                <select id="election_select2" className="form-control form-filter input-sm" >
                                </select>
                            </div>
                            <div className="col-md-4">
                                <select id="province_select2" className="form-control form-filter input-sm" >
                                </select>
                            </div>
                            <div className="col-md-4">
                                <select id="project_select2" className="form-control form-filter input-sm" >
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div className="table-container" style={{ marginTop: "20px" ,backgroundColor:"#c4c4c4" , padding:"20px"}}>
                    <table id="jtr_tagging_datatable" className="table table-condensed table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Voter Name</th>
                                <th className="text-center">JTR Leader </th>
                                <th className="text-center">JTR Member</th>
                                <th className="text-center">Precinct #</th>
                                <th className="text-center">Photo</th>
                                <th className="text-center">Id</th>
                                <th className="text-center">Municipality</th>
                                <th className="text-center">Barangay</th>
                                <th className="text-center">Position</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="voter_name" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                    <select name="is1CheckFilter" onChange={this.handleFilterChange} className="input-sm" style={{ marginTop: "2px" }}>
                                        <option value=''>All</option>
                                        <option value='1'>Yes</option>
                                        <option value='0'>No</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="is2CheckFilter" onChange={this.handleFilterChange} className="input-sm" style={{ marginTop: "2px" }}>
                                        <option value=''>All</option>
                                        <option value='1'>Yes</option>
                                        <option value='0'>No</option>
                                    </select>
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="precinct_no" onChange={this.handleFilterChange} />
                                </td>
                                <td></td>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="municipality_name" onChange={this.handleFilterChange} />
                                </td>

                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="barangay_name" onChange={this.handleFilterChange} />
                                </td>

                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="voter_group" onChange={this.handleFilterChange} />
                                </td>
                               
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
});

window.JtrTaggingDatatable = JtrTaggingDatatable;