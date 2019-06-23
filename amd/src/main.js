define(['jquery'],
    function($) {
        return {
            init: function() {
                $('body:not(.moodlebehat-test) a[href*="report/allylti/launch.php?reporttype=course"]').attr('target', '_blank');
            }
        };
    }
);