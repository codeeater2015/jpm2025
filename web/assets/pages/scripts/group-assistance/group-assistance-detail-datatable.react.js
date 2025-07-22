var GroupAssistanceDetailDatatable = React.createClass({

    getInitialState: function () {
        return {
            showEditModal: false,
            showReleaseModal: false,
            showDetailModal : false,
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
        this.initDatatable();
    },

    initDatatable: function () {
        var self = this;
        var grid = new Datatable();

        var group_assistance_detail_datatable = $("#group_assistance_detail_datatable");
        var grid_project_event = new Datatable();
        var url = Routing.generate("ajax_get_datatable_group_assistance_detail", {}, true);

        grid_project_event.init({
            src: group_assistance_detail_datatable,
            loadingMessage: 'Loading...',
            "dataTable": {
                "bState": true,
                "deferRender": true,
                "pageLength": 100,
                "ajax": {
                    "url": url,
                    "type": 'GET',
                    "data": function (d) {
                        d.municipalityName = $('#group_assistance_detail_datatable input[name="municipalityName"]').val();
                        d.barangayName = $('#group_assistance_detail_datatable input[name="barangayName"]').val();
                        d.occupation = $('#group_assistance_detail_datatable input[name="occupation"]').val();
                        d.clientName = $('#group_assistance_detail_datatable input[name="clientName"]').val();
                        d.dependentName = $('#group_assistance_detail_datatable input[name="dependentName"]').val();
                        d.groupId = self.props.groupId;
                    }
                },
                "columnDefs": [{
                    'orderable': false,
                    'targets': [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }, {
                    'className': 'align-center',
                    'targets': [0, 3]
                }],
                "order": [
                    [1, "desc"]
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
                        "data": "client_name",
                        "className": "text-left",
                        "width": 200,
                    },
                       {
                        "data": "municipality_name",
                        "className": "text-center",
                        "width": 120,
                    },
                    {
                        "data": "barangay_name",
                        "className": "text-center",
                        "width": 150,
                    },
                    {
                        "data": "occupation",
                        "className": "text-center",
                        "width": 120,
                    },
                    {
                        "data": "monthly_income",
                        "className": "text-center",
                        "width": 60,
                    },
                    {
                        "data": "type_of_id",
                        "className": "text-center",
                        "width": 110,
                    },
                    {
                        "data": "dependent_name",
                        "className": "text-left",
                        "width" : 200
                    },
                    {
                        "width": 50,
                        "render": function (data, type, row) {

                            var viewBtn = '<button class="btn btn-xs default edit2-btn"><i class="fa fa-eye"></i></button>';
                            var editBtn = '<button class="btn btn-xs green edit-btn"><i class="fa fa-edit"></i></button>';
                            var assignBtn = '<button class="btn btn-xs blue assign-btn"><i class="fa fa-edit"></i>Assign</button>';
                            var deleteBtn = '<button class="btn btn-xs red-sunglo delete-btn"><i class="fa fa-trash"></i></button>';
                            var btnGroup = '';

                            btnGroup +=  deleteBtn;

                            return btnGroup;
                        },

                        "className": "text-center"
                    }
                ],
            }
        });

        group_assistance_detail_datatable.on('click', '.edit-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showDetailModal: true, target: data.hdr_id });
        });

        group_assistance_detail_datatable.on('click', '.delete-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.delete(data.id);
        });

        self.grid = grid_project_event;
    },

    closeEditModal: function () {
        this.setState({ showEditModal: false, target: null });
        this.reload();
    },

    closeDetailModal: function () {
        this.setState({ showDetailModal: false, target: null });
        this.reload();
    },

    closeReleaseModal: function () {
        this.setState({ showReleaseModal: false, target: null });
    },

    openCreateModal: function () {
        this.setState({ showCreateModal: true });
    },

    openClosingModal: function () {
        this.setState({ showClosingModal: true });
    },

    delete: function (id) {
        var self = this;

        if (confirm("Are you sure you want to delete this transaction?")) {
            self.requestDelete = $.ajax({
                url: Routing.generate("ajax_delete_assistance_hdr", { id: id }),
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

        console.log("relaod databable");
        if (this.grid != null) {
            this.grid.getDataTable().ajax.reload();
        }
    },

    openProfile: function (id) {
        this.setState({ showEditModal: true, target: id });
    },

    isEmpty: function (value) {
        return value == null || value == "" || value == "undefined" || value <= 0;
    },

    render: function () {
        return (
            <div>
                {
                    this.state.showEditModal &&
                    <AssistanceEditModal
                        show={this.state.showEditModal}
                        reload={this.reload}
                        onHide={this.closeEditModal}
                        id={this.state.target}
                    />
                }

                {
                    this.state.showDetailModal &&
                    <GroupAssistanceDetailModal
                        show={this.state.showDetailModal}
                        reload={this.reload}
                        onHide={this.closeDetailModal}
                        id={this.state.target}
                    />
                }

                <div className="table-container" style={{ marginTop: "20px" }}>
                    <table id="group_assistance_detail_datatable" className="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Client</th>
                                <th>Municipality</th>
                                <th>Barangay</th>
                                <th>Occupation</th>
                                <th>Income</th>
                                <th>ID</th>
                                <th>Dependent</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="clientName" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                     <input type="text" className="form-control form-filter input-sm" name="municipalityName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="barangayName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="occupation" onChange={this.handleFilterChange} />
                                </td>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="text" className="form-control form-filter input-sm" name="dependentName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
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

window.GroupAssistanceDetailDatatable = GroupAssistanceDetailDatatable;