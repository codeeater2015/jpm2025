var RenewalDatatable = React.createClass({

    getInitialState: function () {
        return {
            showPhotoModal: false,
            showItemsModal : false,
            targetId: null,
            typingTimer: null,
            doneTypingInterval: 1500,
            user: null,
            gPhotos : 0,
            gLinked : 0,
            gId : 0,
            gUploads : 0,
            gTarget : 0,
            gUnlinked : 0
        }
    },

    componentDidMount: function () {
        this.loadUser(window.userId);
    },

    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.initDatatable);
        });
    },

    initDatatable: function () {
        var self = this;
        var grid = new Datatable();
        var field_photo_table = $("#field_photo_table");

        grid.init({
            src: field_photo_table,
            dataTable: {
                "bState": true,
                "autoWidth": true,
                "serverSide": true,
                "processing": true,
                "deferLoading" : true,
                "ajax": {
                    "url": Routing.generate('ajax_datatable_renewed_id'),
                    "type": "GET",
                    "data": function (d) {
                        d.barangayName = $('#field_photo_table input[name="barangay_name"]').val();
                        d.voterGroup = self.props.voterGroup;
                        d.uploadDate = $('#field_photo_table input[name="upload_date"]').val();
                        d.municipalityName = self.props.municipalityName;

                        self.setState({
                            gPhotos : 0,
                            gLinked : 0,
                            gUnlinked : 0,
                            gId : 0,
                            gUploads : 0,
                            gTarget : 0
                        }); 
                    }
                },
                pageLength: 100,
                columnDefs: [
                    {
                        'className': 'text-center valign-middle',
                        'orderable': false,
                        'targets': [0, 2, 3, 4, 5, 6, 7]
                    }
                ],
                "order": [
                    [1, "asc"]
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
                        "className" : "text-left"
                    },
                    {
                        "data": "municipality_name",
                        "className": "text-center",
                        "width": 120
                    },
                    {
                        "data": "barangay_name",
                        "className": "text-center",
                        "width": 120
                    },
                    {
                        "data": "created_at",
                        "className": "text-center",
                        "width": 50
                    },
                    {
                        "data": "has_new_photo",
                        "className": "text-center",
                        "width": 40
                    },
                    {
                        "data": "has_new_id",
                        "className": "text-center",
                        "width": 40
                    },
                    {
                        "width": 120,
                        "render": function (data, type, row) {
                            var editBtn = '<button class="btn btn-xs green edit-btn"><i class="fa fa-edit"></i></button>';
                            var deleteBtn = '<button class="btn btn-xs red-sunglo delete-btn"><i class="fa fa-trash"></i></button>';
                            var itemsBtn = '<button class="btn btn-xs blue items-btn"><i class="fa fa-file"></i></button>';
                            var downloadBtn = '<button class="btn btn-xs green download-btn"><i class="fa fa-download"></i></button>';

                            var btnGroup = '';
                            btnGroup += itemsBtn;
                           
                                //btnGroup += downloadBtn;
                                btnGroup += deleteBtn;

                            return "";
                        },
                        "className": "text-center"
                    }
                ]
            }

        });


        field_photo_table.on('click', '.edit-btn', function () {
            var data = grid.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showPhotoModal: true, targetId: data.id });
        });

        
        field_photo_table.on('click', '.items-btn', function () {
            var data = grid.getDataTable().row($(this).parents('tr')).data();
            self.setState({ showItemsModal: true, targetId: data.id });
        });

        field_photo_table.on('click', '.delete-btn', function () {
            var data = grid.getDataTable().row($(this).parents('tr')).data();
            self.delete(data.id);
        });

        field_photo_table.on('click', '.download-btn', function () {
            var data = grid.getDataTable().row($(this).parents('tr')).data();
            var url = Routing.generate("ajax_get_download_photo_album", { id: data.id });

            window.location.assign(url);
        });

        self.grid = grid;
    },

    delete: function (id) {
        var self = this;

        if (confirm("continue delete?")) {
            self.requestDelete = $.ajax({
                url: Routing.generate("ajax_delete_field_upload", { id: id }),
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
    
    closeItemsModal:function(){
        this.setState({ showItemsModal : false, targetId : null});
    },

    reload: function () {
        if (this.grid != null) {
            this.grid.getDataTable().ajax.reload();
        }
    },

    isEmpty: function (value) {
        return value == null || value == "" || value == "undefined" || value <= 0;
    },

    closePhotoModal: function () {
        this.setState({ showPhotoModal: false, targetId: null })
    },

    render: function () {
        return (
            <div>
                <div className="table-container">
                    <div className="table-actions-wrapper">
                    </div>
                    <table id="field_photo_table" className="table table-striped table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th className="text-center">No</th>
                                <th className="text-center">Name</th>
                                <th className="text-center">Municipality</th>
                                <th className="text-center">Barangay</th>
                                <th className="text-center">Renew Date</th>
                                <th className="text-center">New Photo</th>
                                <th className="text-center">New ID</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td style={{ padding: "10px 5px" }}>
                                    <input type="text" className="form-control form-filter input-sm" name="barangay_name" onChange={this.handleFilterChange} />
                                </td>
                                <td ></td>
                                <td ></td>
                                <td ></td>
                                <td ></td>
                                <td ></td>
                                <td className="text-center">
                                    <button style={{ marginTop: "5px", marginBottom: "5px" }} className="btn btn-xs green btn-outline filter-submit">
                                        <i className="fa fa-search" />Search
                                    </button>
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

window.RenewalDatatable = RenewalDatatable;