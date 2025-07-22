var OrganizationSummaryComponent = React.createClass({

    getInitialState: function () {
        return {
            provinceCode: null,
            municipalityNo: null,
            brgyNo: null,
            electId : null,
            proId : null,
            createdAt : null,
            displayBarangayTable: false,
            displayMunicipalityTable: false,
            displayProvinceTable: false,
            assignedPrecinct : false,
            summaryData : []
        };
    },

    componentDidMount: function () {
       this.loadData();
    },

    
    loadData: function () {
        var self = this;

        self.requestUser = $.ajax({
            url: Routing.generate("ajax_monitoring_reactivation_summary_by_municipality"),
            type: "GET"
        }).done(function (res) {
            console.log('data has been received');
            console.log(res);
            self.setState({ summaryData: res });
        });
    },


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



    render: function () {
        return (
            <div id="reactivation_summary" className="portlet light portlet-fit bordered">
                <div className="portlet-body">
                   <h3>Reactivation Plan Monitoring</h3>
                   <table className="table table-bordered">
                        <thead>
                            <tr>
                                <th className="text-center">#</th>
                                <th className="text-center">Municipality Name</th>
                                <th className="text-center">Total # Precincts</th>
                                <th className="text-center">Reg Voters</th>
                                <th className="text-center">2022 Members</th>
                                <th className="text-center">Renewed</th>
                                <th className="text-center">New Voter</th>
                                <th className="text-center">New Non Voter</th>
                                <th className="text-center">New Total</th>
                                <th className="text-center">Total</th>
                                <th className="text-center">For Renewal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {this.state.summaryData.map((item,index) => {
                                return (
                                    <tr>
                                        <td className="text-center">{++index}</td>
                                        <td className="text-center">{item.municipality_name}</td>
                                        <td className="text-center">{item.total_precincts}</td>
                                        <td className="text-center">{item.total_registered_voter}</td>
                                        <td className="text-center">{item.total_prev_member}</td>
                                        <td className="text-center">{item.total_renew_member}</td>
                                        <td className="text-center">{item.total_new_voter_member}</td>
                                        <td className="text-center">{item.total_new_nonvoter_member}</td>
                                        <td className="text-center">{item.total_new_member}</td>
                                        <td className="text-center">{Number.parseInt(item.total_new_member) + Number.parseInt(item.total_renew_member)}</td>
                                    </tr>
                                );
                            })}
                        </tbody>
                   </table>
                </div>
            </div>
        )
    },

});

setTimeout(function () {
    ReactDOM.render(
        <OrganizationSummaryComponent />,
        document.getElementById('page-container')
    );
}, 500);
