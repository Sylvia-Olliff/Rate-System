//app/models/programs

module.exports = {
	directory: "/www/websmart/htdocs/wsphp/RateMileQuery/",
	list: {
		builder_Dev: {
			name: "Lane Builder",
			debug: "Dev/TruckRateBuilder_DEV/error.log",
			active: true
		},
		builder_Pro: {
			name: "Lane Builder",
			debug: "Prod/TruckRateBuilder/error.log",
			active: true
		},
		query_Dev: {
			name: "Rate Mile Query",
			debug: "Dev/TruckRateViewer_DEV/error.log",
			active: true	
		},
		query_Pro: {
			name: "Rate Mile Query",
			debug: "Prod/TruckRateViewer/error.log",
			active: false
		}
	}
}