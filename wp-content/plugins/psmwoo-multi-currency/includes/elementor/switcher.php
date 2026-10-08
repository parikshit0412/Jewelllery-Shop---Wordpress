<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

class PSMWOO_MC_Elementor_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'psmmc_switcher';
	}

	/**
	 * Get widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'PSM Multi Currency Switcher', 'psmwoo-multi-currency' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-code';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'woocommerce-elements' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'multi currency', 'woocommerce' );
	}

	/**
	 * Get custom help URL.
	 *
	 * @return string
	 */
	public function get_custom_help_url() {
		return 'https://docs.psmplugins.com/multi-currency';
	}

	/**
	 * Get upsale data.
	 *
	 * @return array
	 */
	protected function get_upsale_data() {
		return array();
	}

	/**
	 * Get script dependencies.
	 * Our switcher render method will add the required script for the frontend.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array();
	}

	/**
	 * Get style dependencies.
	 * Our switcher render method will add the required style for the frontend.
	 * We need to add the style for the backend preview only.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'psmmc-switcher' );
	}

	/**
	 * Register widget controls.
	 *
	 * @return void
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'psmwoo-multi-currency' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		// switcher size.
		$this->add_control(
			'switcherSize',
			array(
				'label'   => esc_html__( 'SIZE', 'psmwoo-multi-currency' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => array(
					'medium' => esc_html__( 'Medium', 'psmwoo-multi-currency' ),
					'small'  => esc_html__( 'Small', 'psmwoo-multi-currency' ),
				),
			)
		);

		// switcher flag.
		$this->add_control(
			'switcherFlag',
			array(
				'label'        => esc_html__( 'COUNTRY FLAG', 'psmwoo-multi-currency' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		// Currency name.
		$this->add_control(
			'switcherCurrencyName',
			array(
				'label'        => esc_html__( 'CURRENCY NAME', 'psmwoo-multi-currency' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		// Currency symbol.
		$this->add_control(
			'switcherCurrencySymbol',
			array(
				'label'        => esc_html__( 'CURRENCY SYMBOL', 'psmwoo-multi-currency' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		// Currency code.
		$this->add_control(
			'switcherCurrencyCode',
			array(
				'label'        => esc_html__( 'CURRENCY CODE', 'psmwoo-multi-currency' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output.
	 *
	 * @return void
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$attributes = array(
			'switcherSize'           => $settings['switcherSize'],
			'switcherFlag'           => $settings['switcherFlag'] === 'yes',
			'switcherCurrencyName'   => $settings['switcherCurrencyName'] === 'yes',
			'switcherCurrencySymbol' => $settings['switcherCurrencySymbol'] === 'yes',
			'switcherCurrencyCode'   => $settings['switcherCurrencyCode'] === 'yes',
		);

		if ( is_admin() ) {

			$width = self::get_switcher_preview_width( $attributes ) . 'px';
			$currency_string = self::get_switcher_preview_currency_string( $attributes );
			?>
			<div className="psmmc-switcher">
				<div className="psmwoo-mc-switcher-container" style="width: <?php echo esc_attr( $width ); ?>">
					<div 
						class="psmwoo-mc-switcher <?php echo esc_attr( $attributes['switcherSize'] ); ?>"
						style="display: flex; justify-content: space-between; align-items: center; gap: 10px;"
					>
						<div style="display: flex; align-items: center; gap: 8px;">
							<?php
							if ( $attributes['switcherFlag'] ) {
								?>
								<img class="flag" src="<?php echo esc_url( PSMWOO_MC_PLUGIN_URL . 'assets/flags/gb.svg' ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="flag">
								<?php
							}
							?>
							<span><?php echo esc_html( $currency_string ); ?></span>
						</div>
						<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" class="angle" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M201.4 374.6c12.5 12.5 32.8 12.5 45.3 0l160-160c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 306.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l160 160z"></path></svg>
					</div>
				</div>
			</div>
			<?php

		} else {
			PSMWOO_MC_Switcher::render( $attributes );
		}
	}

	/**
	 * Get switcher preview width.
	 *
	 * @param array $attributes Widget attributes.
	 * @return int
	 */
	protected static function get_switcher_preview_width( $attributes ) {

		$width = $attributes['switcherSize'] === 'medium' ? 270 : 225;
		if ( ! $attributes['switcherFlag'] ) {
			$width -= $attributes['switcherSize'] === 'medium' ? 30 : 25;
		}
		if ( ! $attributes['switcherCurrencyName'] ) {
			$width -= $attributes['switcherSize'] === 'medium' ? 150 : 125;
		}
		if ( ! $attributes['switcherCurrencySymbol'] ) {
			$width -= $attributes['switcherSize'] === 'medium' ? 15 : 10;
		}
		if ( ! $attributes['switcherCurrencyCode'] ) {
			$width -= $attributes['switcherSize'] === 'medium' ? 30 : 25;
		}

		return $width;
	}

	/**
	 * Get switcher preview currency string.
	 *
	 * @param array $attributes Widget attributes.
	 * @return string
	 */
	protected static function get_switcher_preview_currency_string( $attributes ) {

		$name = '';

		if ( $attributes['switcherCurrencyName'] && $attributes['switcherCurrencySymbol'] ) {
			$name = __( 'Pound Sterling', 'psmwoo-multi-currency' ) . ' (£)';
		}
		if ( $attributes['switcherCurrencyName'] && ! $attributes['switcherCurrencySymbol'] ) {
			$name = __( 'Pound Sterling', 'psmwoo-multi-currency' );
		}
		if ( ! $attributes['switcherCurrencyName'] && $attributes['switcherCurrencySymbol'] ) {
			$name = '£';
		}
		if ( $attributes['switcherCurrencyCode'] ) {
			if ( $attributes['switcherCurrencyName'] ) {
				$name .= ' - GBP';
			} elseif ( $attributes['switcherCurrencySymbol'] ) {
				$name .= ' GBP';
			} else {
				$name .= 'GBP';
			}
		}
		return $name;
	}
}
