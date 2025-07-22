var FormGroup = ReactBootstrap.FormGroup
var HelpBlock = ReactBootstrap.HelpBlock;
var ControlLabel = ReactBootstrap.ControlLabel;
var FormControl = ReactBootstrap.FormControl;

var PhotoUpload = React.createClass({

    notify: function (message, color) {
        $.notific8('zindex', 11500);
        $.notific8(message, {
            heading: 'System Message',
            color: color,
            life: 5000,
            verticalEdge: 'right',
            horizontalEdge: 'top',
        });
    },

    componentDidMount: function () {
        var myDropzone = new Dropzone("#photo-uploader", { url: Routing.generate("ajax_field_photo_upload", {}) });

        this.dropzone = myDropzone;
        this.loadUser(window.userId);
        this.initSelect2();
    },

    getInitialState: function () {
        return {
            municipalityName: null,
            brgyNo: null,
            showDatatable: false
        };
    },

    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.initSelect2);
        });
    },

    initSelect2: function () {
        var self = this;

        $("#form-municipality-select2").select2({
            casesentitive: false,
            placeholder: "Enter municipality...",
            width: '100%',
            allowClear: true,
            tags: true,
            containerCssClass: ":all:",
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            ajax: {
                url: Routing.generate('ajax_select2_municipality'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: 53
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.name, text: item.name };
                        })
                    };
                },
            }
        });

        $("#form-barangay-select2").select2({
            casesentitive: false,
            placeholder: "Enter name...",
            allowClear: true,
            delay: 1500,
            width: '100%',
            containerCssClass: ':all:',
            ajax: {
                url: Routing.generate('ajax_select2_barangay_alt'),
                data: function (params) {
                    return {
                        searchText: params.term,
                        provinceCode: 53,
                        municipalityName: $("#form-municipality-select2").val()
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.map(function (item) {
                            return { id: item.brgy_no, text: item.name };
                        })
                    };
                },
            }
        });

        $("#form-municipality-select2").on("change", function () {
            $("#form-barangay-select2").empty()
                .trigger("change");

            self.setState({ "municipalityName": $(this).val() });
            self.setState({ showDatatable : true });
            self.reloadDatatable();

            console.log("showing datatable");
        });

        $("#form-barangay-select2").on("change", function () {
            self.dropzone.destroy();
            var myDropzone = new Dropzone("#photo-uploader", {
                url: Routing.generate("ajax_field_photo_upload_v2", {
                    brgyNo: $(this).val(),
                    municipalityName : $("#form-municipality-select2").val()
                })
            });

            self.dropzone = myDropzone;
            self.setState({ "brgyNo": $(this).val() });
        
            console.log("show databable");

        });
    },

    reloadDatatable: function () {
        console.log("reloading datatable");
        if (this.state.showDatatable)
            this.refs.fieldDatatable.reload();
    },

    render: function () {
        return (
            <div>
                <div className="row">
                    <div className="col-md-8">
                        <div className="portlet light portlet-fit bordered">
                            <div className="portlet-body">
                                <div className="row">
                                    <div className="col-md-8">
                                        <h4><strong>Special Operations Photo Uploads</strong></h4>
                                    </div>
                                </div>
                                {this.state.showDatatable &&
                                    <FieldUploadDatatable ref="fieldDatatable"  municipalityName={this.state.municipalityName} />
                                }
                            </div>
                        </div>
                    </div>
                    <div className="col-md-4">
                        <div className="portlet light portlet-fit bordered">
                            <div className="portlet-body">
                                <h4><strong>Upload Images Here</strong></h4>

                                <FormGroup controlId="formBarangayNo">
                                    <ControlLabel > Municipality : </ControlLabel>
                                    <select id="form-municipality-select2" className="form-control input-sm">
                                    </select>
                                </FormGroup>

                                <FormGroup controlId="formBarangayNo">
                                    <ControlLabel > Barangay : </ControlLabel>
                                    <select id="form-barangay-select2" className="form-control input-sm">
                                    </select>
                                </FormGroup>

                                <form action="/file-upload"
                                    className="dropzone"
                                    id="photo-uploader">
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        )
    }
});

setTimeout(function () {
    ReactDOM.render(
        <PhotoUpload />,
        document.getElementById('page-container')
    );
}, 500);
