var RaffleComponent = React.createClass({

    getInitialState: function () {
        return {
            selectedNumber: null,
            data: [
                {
                    voterName: "NAME 1"
                },
                {
                    voterName: "NAME 2"
                },
                {
                    voterName: "NAME 2"
                },
                {
                    voterName: "NAME 3"
                },
                {
                    voterName: "NAME 4"
                },
                {
                    voterName: "NAME 5"
                },
                {
                    voterName: "NAME 6"
                }
            ]
        };
    },

    componentDidMount: function () {
        this.play();
    },

    random: function (min, max) {
        return Math.floor((Math.random()) * (max - min + 1)) + min;
    },

    generateRandomNumbers: function () {
        let data = this.state.data;
        console.log("generated random number");
        this.setState({ selectedNumber: this.random(0, data.length) });
    },

    play: function () {
        setInterval(this.generateRandomNumbers, 50);
    },

    render: function () {
        return (
            <div className="portlet light portlet-fit bordered">
                <div className="portlet-body">
                    <div className="text-center" style={{ fontSize:"200px", fontWeight:"bold" }}>
                       {this.state.selectedNumber}
                    </div>
                </div>
            </div>
        )
    }
});

setTimeout(function () {
    ReactDOM.render(
        <RaffleComponent />,
        document.getElementById('page-container')
    );
}, 500);
