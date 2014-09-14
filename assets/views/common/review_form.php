<div class="modal fade" id="reviewForm" tabindex="-1" role="dialog" aria-labelledby="reviewForm" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <form class="form-horizontal js-review-form" role="form" method="POST" action="/review/send" id="sendForm">
                    <input type="hidden" required name="productID" id="productID" value="<?php echo $product->productID; ?>">
                    <fieldset>
                        <legend class="text-left">Review Form</legend>
                        <div class="form-group">
                            <div class="col-md-6 field-group">
                                <?php
                                $user = $this->pixie->auth->user();
                                $readonly = !is_null($user) ? 'readonly' : '';
                                $name = !is_null($user) ? $user->username : '';
                                $email = !is_null($user) ? $user->email : '';
                                ?>
                                <input type="text" maxlength="100" required class="form-control" placeholder="Name" name="userName" id="userName" <?php echo $readonly; ?> value="<?php $_($name, 'name'); ?>">
                            </div>
                            <div class="col-md-6 field-group">
                                <input type="email" maxlength="100" required class="form-control" placeholder="Email" name="userEmail" id="userEmail" <?php echo $readonly && $user->email ? $readonly : ''; ?> value="<?php $_($email, 'email'); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 text-left">
                                <input name="starValue" id="starValue" type="number" class="rating" min=0 max=5 step=1
                                       data-size="xs" data-rtl="false">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <textarea class="form-control form-text-review" id="textReview" name="textReview"
                                          placeholder="Input your review here..." required maxlength="500"
                                          data-bv-container="tooltip" ></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-3">
                                <button type="submit" name="sendreview" id="sendreview"
                                        class="btn btn-primary btn-block">Send review
                                </button>
                            </div>
                        </div>
                    </fieldset>
                    <?php $_token('review'); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $('.js-review-form').bootstrapValidator({
            exclude: ['sendreview'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            container: 'tooltip',
            fields: {
                textReview: {
                    message: 'Please write your review'
                },
                userName: {
                    group: '.field-group'
                },
                userEmail: {
                    group: '.field-group'
                }
            }
        });
    });
</script>