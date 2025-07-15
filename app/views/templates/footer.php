    </div>
</div>

    <!-- google web fonts -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Source+Code+Pro:400,700:latin',
                    'Roboto:400,300,500,700,400italic:latin'
                ]
            }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

    <!-- common functions -->
    <script src="<?= $PATH ?>assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="<?= $PATH ?>assets/js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    <script src="<?= $PATH ?>assets/js/altair_admin_common.min.js"></script>

    <script src="<?= $PATH ?>bower_components/parsleyjs/dist/parsley.min.js"></script>
    <!-- jquery steps -->
    <script src="<?= $PATH ?>assets/js/custom/wizard_steps.min.js?1"></script>
    <script src="<?= $PATH ?>assets/js/kendoui_custom.min.js"></script>

    <!--  forms wizard functions -->
    <script src="<?= $PATH ?>assets/js/scripts.js?1"></script>


    <script>
        $(function() {
            // enable hires images
            altair_helpers.retina_images();
            // fastClick (touch devices)
            if(Modernizr.touch) {
                FastClick.attach(document.body);
            }
        });
    </script>

</body>
</html>