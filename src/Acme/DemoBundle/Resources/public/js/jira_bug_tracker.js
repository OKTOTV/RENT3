// Requires jQuery!
jQuery.ajax({
    url: "http://jira.okto.tv/s/de_DE-c3xu1b-418945332/849/16/1.2.9/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs.js?collectorId=2cd0eea9",
    type: "get",
    cache: true,
    dataType: "script"
});

 window.ATL_JQ_PAGE_PROPS =  {
	"triggerFunction": function(showCollectorDialog) {
		//Requries that jQuery is available! 
		jQuery("#bugtracker").click(function(e) {
			e.preventDefault();
			showCollectorDialog();
		});
	}};
