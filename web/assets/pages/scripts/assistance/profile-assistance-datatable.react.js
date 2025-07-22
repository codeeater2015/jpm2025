var ProfileAssistanceDatatable = React.createClass({

    getInitialState: function () {
        return {
            showEditModal: false,
            showReleaseModal: false,
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

        var jpm_assistance_datatable = $("#jpm_profile_assistance_datatable");
        var grid_project_event = new Datatable();
        var url = Routing.generate("ajax_get_datatable_profile_assistance", {}, true);

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
                        d.clientName = self.props.fullname;
                        d.dependentName = self.props.fullname;
                        d.controlNo = $('#jpm_profile_assistance_datatable input[name="controlNo"]').val();
                        d.hospital = $('#jpm_profile_assistance_datatable input[name="hospital"]').val();
                        d.transDate = $('#jpm_profile_assistance_datatable input[name="transDate"]').val();
                    }
                },
                "columnDefs": [{
                    'orderable': false,
                    'targets': [0, 2, 3, 4, 5, 6, 7, 8]
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
                        "data": "trans_date",
                        "className": "text-center",
                        "width": 80,
                    },
                       {
                        "data": "control_no",
                        "className": "text-center",
                        "width": 80,
                    },
                    {
                        "data": "client_name",
                        "className": "text-left",
                    },
                    {
                        "data": "dependent_name",
                        "className": "text-left",
                    },
                    {
                        "data": "hospital",
                        "className": "text-center",
                        "width": 100,
                    },
                    {
                        "data": "final_bill",
                        "className": "text-center",
                        "width": 50,
                    },
                    {
                        "data": "amount",
                        "className": "text-center",
                        "width": 50,
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
            self.setState({ showEditModal: true, target: data.id });
        });

        jpm_assistance_datatable.on('click', '.delete-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.delete(data.id);
        });

        self.grid = grid_project_event;
    },

    closeEditModal: function () {
        this.setState({ showEditModal: false, target: null });
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
                <div className="table-container" style={{ marginTop: "20px" }}>
                    <table id="jpm_profile_assistance_datatable" className="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Ctrl #</th>
                                <th>Client Name</th>
                                <th>Patient Name</th>
                                <th>Hospital</th>
                                <th>Bill</th>
                                <th>Granted</th>
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
                                     <input type="text" className="form-control form-filter input-sm" name="hospital" onChange={this.handleFilterChange} />
                                </td>
                                
                                <td></td>
                                <td></td>
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

window.ProfileAssistanceDatatable = ProfileAssistanceDatatable;