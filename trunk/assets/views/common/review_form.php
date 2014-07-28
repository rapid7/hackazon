<div class="modal fade" id="reviewForm" tabindex="-1" role="dialog" aria-labelledby="reviewForm" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form class="form-horizontal">
                    <fieldset>
                        <legend class="text-left">Review Form</legend>
                        <div class="form-group">
                            <div class="col-md-12 text-left">
                                <input id="startValue" type="number" class="rating" min=0 max=5 step=1
                                       data-size="xs" data-rtl="false">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea class="form-control" id="textReview" name="textarea"
                                          placeholder="Input your review here..." required maxlength="500"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3">
                                <button name="sendreview" id="sendreview" class="btn btn-primary btn-block">Send review</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>