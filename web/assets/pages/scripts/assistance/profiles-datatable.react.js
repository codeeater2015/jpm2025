var ProfilesDatatable = React.createClass({

    getInitialState: function () {
        return {
            showEditModal: false,
            showDetailModal: false,
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

        var profiles_datatable = $("#profiles_datatable");
        var grid_project_event = new Datatable();
        var url = Routing.generate("ajax_get_datatable_assistance_profile", {}, true);

        grid_project_event.init({
            src: profiles_datatable,
            loadingMessage: 'Loading...',
            "dataTable": {
                "bState": true,
                "deferRender": true,
                "pageLength": 100,
                "ajax": {
                    "url": url,
                    "type": 'GET',
                    "data": function (d) {
                        d.municipalityName = $('#profiles_datatable input[name="municipalityName"]').val();
                        d.barangayName = $('#profiles_datatable input[name="barangayName"]').val();
                        d.fullname = $('#profiles_datatable input[name="fullname"]').val();
                        d.voterName = $('#profiles_datatable input[name="voterName"]').val();
                        d.purok = $('#profiles_datatable input[name="purok"]').val();
                        d.birthdate = $('#profiles_datatable input[name="birthdate"]').val();
                        d.contactNo = $('#profiles_datatable input[name="contactNo"]').val();
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
                        "data": "fullname",
                        "className": "text-left",
                         "width": 150,
                    },
                       {
                        "data": "voter_name",
                        "className": "text-left",
                        "width": 150,
                    },
                    {
                        "data": "municipality_name",
                        "className": "text-center",
                        "width": 80,

                    },
                    {
                        "data": "barangay_name",
                        "className": "text-center",
                        "width": 80,
                    },
                    {
                        "data": "purok",
                        "className": "text-center",
                        "width": 100,
                    },
                    {
                        "data": "contact_no",
                        "className": "text-center",
                        "width": 70,
                    },
                    {
                        "data": "birthdate",
                        "className": "text-center",
                        "width": 80,
                    },
                    {
                        "width": 80,
                        "render": function (data, type, row) {

                            var viewBtn = '<button class="btn btn-xs default profile-view-btn"><i class="fa fa-eye"></i></button>';
                            var editBtn = '<button class="btn btn-xs green edit-btn"><i class="fa fa-edit"></i></button>';
                            var assignBtn = '<button class="btn btn-xs blue assign-btn"><i class="fa fa-edit"></i>Assign</button>';
                            var deleteBtn = '<button class="btn btn-xs red-sunglo delete-btn"><i class="fa fa-trash"></i></button>';
                            var btnGroup = '';

                            btnGroup +=  viewBtn + editBtn + deleteBtn;

                            return btnGroup;
                        },

                        "className": "text-center"
                    }
                ],
            }
        });

        profiles_datatable.on('click', '.profile-view-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            console.log('view item');
            console.log(data);
            self.setState({ showDetailModal: true, target: data.id });
        });

        profiles_datatable.on('click', '.edit-btn', function () {
            var data = grid_project_event.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showEditModal: true, target: data.id });
        });

        profiles_datatable.on('click', '.delete-btn', function () {
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

    closeDetailModal: function () {
        this.setState({ showDetailModal: false, target : null });
    },

    openClosingModal: function () {
        this.setState({ showClosingModal: true });
    },

    delete: function (id) {
        var self = this;

        if (confirm("Are you sure you want to delete this profile?")) {
            self.requestDelete = $.ajax({
                url: Routing.generate("ajax_delete_assistance_profile", { id: id }),
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
                    <AssistanceProfileEditModal
                        show={this.state.showEditModal}
                        reload={this.reload}
                        onHide={this.closeEditModal}
                        id={this.state.target}
                    />
                }

                {
                    this.state.showDetailModal &&
                    <AssistanceProfileDetailModal
                        show={this.state.showDetailModal}
                        onHide={this.closeDetailModal}
                        id={this.state.target}
                    />
                }

                <div className="table-container" style={{ marginTop: "20px" }}>
                    <table id="profiles_datatable" className="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Voter Name</th>
                                <th>Municipality</th>
                                <th>Barangay</th>
                                <th>Purok</th>
                                <th>Contact No</th>
                                <th>Birthdate</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="fullname" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                     <input type="text" className="form-control form-filter input-sm" name="voterName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="municipalityName" onChange={this.handleFilterChange} />
                                </td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="barangayName" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                    <input type="text" className="form-control form-filter input-sm" name="purok" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                    <input type="text" className="form-control form-filter input-sm" name="contactNo" onChange={this.handleFilterChange} />
                                </td>
                                <td>
                                     <input type="text" className="form-control form-filter input-sm" name="birthdate" onChange={this.handleFilterChange} />
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

window.ProfilesDatatable = ProfilesDatatable;