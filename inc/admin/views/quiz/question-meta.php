<?php
/**
 * Question options template.
 *
 * @since 3.0.0
 */
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"
    integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
    integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

<script type="text/x-template" id="tmpl-lp-quiz-question-meta">
    <div class="quiz-question-options">
		<div class="postbox" @click="openSettings($event)">
			<h2 class="hndle"><span><?php esc_html_e( 'Details', 'learnpress' ); ?></span> </h2>
			<a class="toggle" @click.prevent="openSettings($event)"></a>
			<div class="inside">
				<div class="lp-quiz-editor__detail">
					<div class="lp-quiz-editor__detail-field">
						<div class="lp-quiz-editor__detail-label">
							<label :for="'content-'+question.id"><?php esc_html_e( 'Description', 'learnpress' ); ?></label>
						</div>
						<div class="lp-quiz-editor__detail-input">
							<div>
								<textarea name="" :id="'content-'+question.id" cols="60" rows="3" class="lp-quiz-editor__detail-textarea large-text" @change="updateContent" v-model="question.settings.content"></textarea>
							</div>
						</div>
					</div>
					<div class="lp-quiz-editor__detail-field">
						<div class="lp-quiz-editor__detail-label">
							<label :for="'marking-'+question.id"><?php esc_html_e( 'Points', 'learnpress' ); ?></label>
						</div>
						<div class="lp-quiz-editor__detail-input">
							<div>
								<input name="mark" :id="'marking-'+question.id" type="number" min="1" step="1" v-model="question.settings.mark" @change="updateMeta">
								<p class="description"><?php esc_html_e( 'Points for choosing the correct answer.', 'learnpress' ); ?></p>
							</div>
						</div>
					</div>
					<div class="lp-quiz-editor__detail-field">
						<div class="lp-quiz-editor__detail-label">
							<label :for="'hint-'+question.id"><?php esc_html_e( 'Hint', 'learnpress' ); ?></label>
						</div>
						<div class="lp-quiz-editor__detail-input">
							<div>
								<textarea name="hint" :id="'hint-'+question.id" cols="60" rows="3" class="rlp-quiz-editor__detail-textarea large-text" @change="updateMeta" v-model="question.settings.hint"></textarea>
								<p class="description"><?php esc_html_e( 'Instruction for user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'learnpress' ); ?></p>
							</div>
						</div>
					</div>
					<div class="lp-quiz-editor__detail-field">
						<div class="lp-quiz-editor__detail-label">
							<label :for="'explanation-'+question.id"><?php esc_html_e( 'Explanation', 'learnpress' ); ?></label>
						</div>
						<div class="lp-quiz-editor__detail-input">
							<div>
								<textarea name="explanation" :id="'explanation-'+question.id" cols="60" rows="3" class="lp-quiz-editor__detail-textarea large-text" @change="updateMeta" v-model="question.settings.explanation"></textarea>
								<p class="description"><?php esc_html_e( 'Explanation will be displayed when students click button "Check Answer".', 'learnpress' ); ?></p>
							</div>
						</div>
					</div>
					<!--
						Customized
						لنصوص القدرات اللفظي
					-->
					<div class="lp-quiz-editor__detail-field">
						<div class="lp-quiz-editor__detail-label">
							<label :for="'paragraph-'+question.id">قطعة استيعاب المقروء</label>
						</div>
						<div class="lp-quiz-editor__detail-input">
							<div>
								<select class="paragraph_menu" name="paragraph" :id="'paragraph-'+question.id"  @change="updateMeta" v-model="question.settings.paragraph">
									<option disabled selected value="">إختر القطعة</option>
									<?php
										// Get the 'Profiles' post type
										$args = array(
											'post_type' => 'paragraph',
                                            'posts_per_page'   => -1,
										);
										$loop = new WP_Query($args);
										while($loop->have_posts()): $loop->the_post();
									?>
									<option value="<?= get_the_ID(); ?>"><?= the_title() ?></option>
									<?php
										endwhile;
										wp_reset_query();
									?>
								</select>
								<p class="description">اختار قطعة استيعاب المقروء</p>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">
jQuery(function($) {
    var $Vue = window.$Vue || Vue;
    var $store = window.LP_Quiz_Store;
    $(document).ready(function() {

        setTimeout(() => {
            // get all paragraph menus (get all elements their id starts with paragraph-)
            const paragraph = document.querySelectorAll(`[id^="paragraph-"]`);
            const paragrahs_id = [];
            for (var i = 0; i < paragraph.length; i++) {
                // paragraph- = 10 characters , we need to remove it to get the id
                paragrahs_id.push(paragraph[i].id.slice(10))
            }

            $.ajax({
                type: "POST",
                url: "<?= admin_url('admin-ajax.php?action=check_relational_array'); ?>",
                data: {
                    data: JSON.stringify(paragrahs_id)
                },
                cache: false,

                success: function(response) {
                    // get the paragraph_id array
                    const questions_paragraph = JSON.parse(response)
                    // loop into the paragrah menu elements
                    for (var i = 0; i < paragraph.length; i++) {
                        let element = paragraph[i];
                        let paragraph_id = questions_paragraph[i];
                        // check if the option value is equal to the paragraph id
                        $("#" + element.id + " option").each(function(paragrah_id) {
                            if (paragraph_id == $(this).val()) {
                                // remove the default selected option 
                                $("#" + element.id + " option:selected")
                                    .removeAttr("selected");
                                // add "selected" attribute to this option
                                $(this).attr("selected", "selected")
                            }
                        });

                    }
                    $('select').selectize({
                        sortField: 'text'
                    });
                }
            });

        }, 500);

    })

    $Vue.component('lp-quiz-question-meta', {
        template: '#tmpl-lp-quiz-question-meta',
        props: ['question'],
        methods: {
            updateContent: function() {
                $store.dispatch('lqs/updateQuestionContent', this.question);
            },
            updateMeta: function(e) {
                if (e.target.name == 'paragraph') {
                    let id = this.question.id;
                    var settings = {
                        "url": "<?= admin_url('admin-ajax.php?action=change_paragraph&question_id='); ?>" +
                            id + "&paragraph_id=" + e.target.options[e.target.options
                                .selectedIndex].value,
                        "method": "GET",
                        "timeout": 0,
                    };

                    $.ajax(settings).done(function(response) {
                        console.log(response);
                    });
                } else {
                    $store.dispatch('lqs/updateQuestionMeta', {
                        question: this.question,
                        meta_key: e.target.name
                    });
                }
            },
            openSettings: function(e) {
                e.stopPropagation();

                var $root = $(this.$el).closest('.question-settings'),
                    $postbox = $root.find('.postbox');

                $postbox.removeClass('closed');

                if (!$(e.target).hasClass('toggle')) {
                    return;
                }

                var isClosed = $root.toggleClass('closed').hasClass('closed');

                $store.dispatch('lqs/updateQuizQuestionsHidden', {
                    hidden: $('.question-settings.closed').map(function() {
                        return $(this).closest('.question-item').data('item-id');
                    }).get()
                });
            }
        }
    });
});
</script>