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

    loadUser: function (userId) {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_get_user", { id: userId }),
            type: "GET"
        }).done(function (res) {
            self.setState({ user: res }, self.initSelect2);
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
                    <div className="col-md-12">
                        <div className="portlet light portlet-fit bordered">
                            <div className="portlet-body">
                                <h4><strong>JTR Tagging</strong></h4>
                                <JtrTaggingDatatable/>
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
