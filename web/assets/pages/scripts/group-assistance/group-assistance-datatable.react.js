var GroupAssistanceDatatable = React.createClass({

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

        var jpm_assistance_datatable = $("#jpm_assistance_datatable");
        var grid_project_event = new Datatable();
        var url = Routing.generate("ajax_get_datatable_group_assistance", {}, true);

        grid_project_event.init({
            src: jpm_assistance_datatable,
            loadingMessage: 'Loading...',
            "dataTable": {
                "bState": true,
                "deferRender": true,
                "pageLength": 100,
                "ajax": {
                    "url": url,
                    "type": 'GET',
                    "data": function (d) {
                        d.municipalityName = $('#jpm_assistance_datatable input[name="municipalityName"]').val();
                        d.barangayName = $('#jpm_assistance_datatable input[name="barangayName"]').val();
                        d.clientName = $('#jpm_assistance_datatable input[name="clientName"]').val();
                        d.patientName = $('#jpm_assistance_datatable input[name="patientName"]').val();
                        d.controlNo = $('#jpm_assistance_datatable input[name="controlNo"]').val();
                        d.hospital = $('#jpm_assistance_datatable input[name="hospital"]').val();
                        d.transDate = $('#jpm_assistance_datatable input[name="transDate"]').val();
                    }
                },
                "columnDefs": [{
                    'orderable': false,
                    'targets': [0, 1, 2, 3, 4, 5, 6]
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
                        "data": "municipality_name",
                        "className": "text-center",
                        "width": 120,
                    },
                       {
                        "data": "assist_type",
                        "className": "text-center",
                        "width": 100,
                    },
                    {
                        "data": "batch_date",
                        "className": "text-center",
                        "width": 100,
                    },
                    {
                        "data": "batch_label",
                        "className": "text-center",
                        "width": 250,
                    },
                    {
                        "data": "remarks",
                        "className": "text-center"
                    },
                    {
                        "width": 80,
                        "render": function (data, type, row) {

                            var viewBtn = '<button class="btn btn-xs default edit2-btn"><i class="fa fa-eye"></i></button>';
                            var editBtn = '<button class="btn btn-xs green edit-btn"><i class="fa fa-edit"></i></button>';
                            var assignBtn = '<button class="btn btn-xs blue assign-btn"><i class="fa fa-edit"></i>Assign</button>';
                            var deleteBtn = '<button class="btn btn-xs red-sunglo delete-btn"><i class="fa fa-trash"></i></button>';
                            var btnGroup = '';

                            btnGroup += editBtn + deleteBtn;

                            return btnGroup;
                        },

                        "className": "text-center"
                    }
                ],
            }
        });

        jpm_assistance_datatable.on('click', '.edit-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showDetailModal: true, target: data.hdr_id });
        });

        jpm_assistance_datatable.on('click', '.delete-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.delete(data.hdr_id);
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
                url: Routing.generate("ajax_delete_group_assistance_hdr", { id: id }),
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
                    <table id="jpm_assistance_datatable" className="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Municipality</th>
                                <th>Assistance</th>
                                <th>Batch Date</th>
                                <th>Batch Name</th>
                                <th>Remarks</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="transDate" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                     <input type="text" className="form-control form-filter input-sm" name="controlNo" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="clientName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="dependentName" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                    <input type="text" className="form-control form-filter input-sm" name="municipalityName" onChange={this.handleFilterChange} />
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

window.GroupAssistanceDatatable = GroupAssistanceDatatable;