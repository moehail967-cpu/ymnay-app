<footer class="fn-footer">
    <div class="fn-footer-top">
        <div class="container">
            <div class="row gy-4 justify-content-between">
                {!! render_frontend_sidebar('footer', ['column' => true]) !!}
            </div>
        </div>
    </div>

    <div class="fn-footer-bottom">
        <div class="container">
            <div class="row gy-3 align-items-center justify-content-center">
                {!! render_frontend_sidebar('footer_bottom_left', ['column' => true]) !!}

                <div class="col-lg-4">
                    <div class="fn-footer-copy">
                        {!! get_footer_copyright_text() !!}
                    </div>
                </div>

                {!! render_frontend_sidebar('footer_bottom_right', ['column' => true]) !!}
            </div>
        </div>
    </div>
</footer>
