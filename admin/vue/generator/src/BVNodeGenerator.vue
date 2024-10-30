<template>

	<div class="dki4wp-generator__fields-columns">
		<div class="dki4wp-generator__fields">

			<div class="dki4wp-generator__fields-title">Base fields</div>
				<label class="dki4wp-generator__label" v-on:mouseover="setInfo('param')" v-on:mouseout="clearInfo()">
					<span>Param Name:</span>
					<input type="text" v-model="fields.param.value" placeholder="your_param" v-on:focus="lockInfo('param')" v-on:blur="unlockInfo()" />
				</label>

				
				<label class="dki4wp-generator__label" v-on:mouseover="setInfo('default')" v-on:mouseout="clearInfo()">
					<span>Default Value:</span>
					<input type="text" v-model="fields.default.value" placeholder="Default Value" v-on:focus="lockInfo('default')" v-on:blur="unlockInfo()" />
				</label>

				<div class="dki4wp-generator__fields-title">Modifications</div>
				
			<label class="dki4wp-generator__label" v-on:mouseover="setInfo('prefix')" v-on:mouseout="clearInfo()">
				<span>Prefix:</span>
				<input type="text" v-model="fields.prefix.value" placeholder="Prefix" v-on:focus="lockInfo('prefix')" v-on:blur="unlockInfo()" />
			</label>

			<label class="dki4wp-generator__label" v-on:mouseover="setInfo('sufix')" v-on:mouseout="clearInfo()">
				<span>Sufix:</span>
				<input type="text" v-model="fields.sufix.value" placeholder="Sufix" v-on:focus="lockInfo('sufix')" v-on:blur="unlockInfo()" />
			</label>

			<label class="dki4wp-generator__label" v-on:mouseover="setInfo('style')" v-on:mouseout="clearInfo()">
				<span>Style:</span>
				<select v-model="fields.style.selected" v-on:focus="lockInfo('style')" v-on:blur="unlockInfo()">
					<option v-for="option in fields.style.options" :key="option.value" :value="option.value">
						{{ option.text }}
					</option>
				</select>

			</label>
			

				<div class="dki4wp-generator__fields-title">Advanced Settings</div>



			<label class="dki4wp-generator__label" v-on:mouseover="setInfo('expires')" v-on:mouseout="clearInfo()">
				<span>Persist the resulted value:</span>
				<select v-model="fields.expires.selected" v-on:focus="lockInfo('expires')" v-on:blur="unlockInfo()" >
					<option v-for="option in fields.expires.options" :key="option.value" :value="option.value" >
						{{ option.text }}
					</option>
				</select>
			</label>

			<label v-if="fields.expires.selected == 'custom'" class="dki4wp-generator__label" v-on:mouseover="setInfo('expires')" v-on:mouseout="clearInfo()">
				<span>Custom Keep Value:</span>
				<input type="text" v-model="fields.expires.custom" placeholder="3 Months" v-on:focus="lockInfo('expires')" v-on:blur="unlockInfo()" />
			</label>
			
			<label class="dki4wp-generator__label dki4wp-generator__label--full-width" v-on:mouseover="setInfo('cache')" v-on:mouseout="clearInfo()">
				<span>Prevent caching:</span>
				<input type="checkbox" v-model="fields.cache.value"  v-on:focus="lockInfo('cache')" v-on:blur="unlockInfo()" />
				<span class="checkbox-visible"></span>
			</label>

		</div>
		<div class="dki4wp-generator__info">
			<label class="dki4wp-generator__label">
				<span>Info:</span>
				<div class="dki4wp-generator__info-panel" v-html="getInfo()"></div>
			</label>
			
		</div>
	</div>



	<div class="dki4wp-generator__pseuo-label">Your Shortcode:</div>

	<div class="shortcode-textarea-wrapper">
		<textarea class="shortcode-textarea" :value="shortcode()" readonly></textarea>
		<button class="shortcode-textarea-wrapper-button" v-on:click="copyShortcode()">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
				<path d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM112 192H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
			</svg>
			<span class="shortcode-textarea-wrapper-button__tooltip" v-if="!copied">Copy to Clipboard</span>
			<span class="shortcode-textarea-wrapper-button__tooltip" v-if="copied">Copied!</span>
		</button>
	</div>
	<div class="dki4wp-generator__fields">
		
	<label class="dki4wp-generator__label">
		<span>Test Value:</span>
		<input type="text" v-model="fields.test.value" placeholder="Test Value" />
	</label>
	</div>

	<div class="dki4wp-generator__pseuo-label">Result:</div>

	<div class="shortcode-evaluate-wrapper">
		"{{ evaluate() }}"
	</div>

</template>

<script>

export default {
	name: 'BVNodeGenerator',
	props: [],

	data() {
		return {
			copied: false,
			info: {
				field: null,
				lock: false,
			},
			fields: {
				param: {
					value: '',
					info: `<div>
<strong>param</strong></div><div><p>The name of param used to bind query parameters or set values. It's can be anything except the <a href="https://codex.wordpress.org/Reserved_Terms" target="_blank" rel="nofollow noopener">Reserved Terms used by WordPress</a>.</p></div>`
				},
				style: {
					type: 'select',
					info: `<div>
<strong>style (optional)</strong></div><div><p>Defines the style of output.</p></div><ul class="shortcode-doc__param-values"><li class="shortcode-doc__param-value">
<strong>
empty | normal                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Does not change the param value.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
upper                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Changes the param value to uppercase.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
lower                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Changes the param value to lowercase.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
title                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Changes the params words first letters to uppercase.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
sentence                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Changes the params first letter to uppercase.</p>
</span></li></ul>`,
					options: [
						{ value: '', text: 'normal' },
						{ value: 'upper', text: 'upper' },
						{ value: 'lower', text: 'lower' },
						{ value: 'title', text: 'title' },
						{ value: 'sentence', text: 'sentence' }
					],
					selected: ''
				},
				prefix: {
					value: '',
					info: `<div>
<strong>prefix (optional)</strong></div><div><p>Adds a prefix to the resulted value in case it isn't the default value.</p></div>`
				},
				sufix: {
					value: '',
					info: `<div>
<strong>sufix (optional)</strong></div><div><p>Adds a sufix to the resulted value in case it isn't the default value.</p></div>`
				},
				cache: {
					info: `<div>
<strong>cache (optional)</strong></div><div><p>When WordPress is using page caching (like LSCache or WPRocket) you can force a shortcode to reload right after the page is loaded to a non-cached value.</p></div><ul class="shortcode-doc__param-values"><li class="shortcode-doc__param-value">
<strong>
empty | true                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Default WordPress caching behavior.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
false                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Processes value through a non-cachable AJAX request right after the page is rendered in user browser.</p>
</span></li></ul>`,
					value: false,
				},
				expires: {
					info: `<div>
<strong>keep (optional)</strong></div><div>    <p>Allow to persist the resulted value across all shortcodes of this param.</p></div><ul class="shortcode-doc__param-values"><li class="shortcode-doc__param-value">
<strong>
<em>empty</em> | false                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>A value will be processed every time a shortcode is parsed.</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
session | true                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>A value will be cached until the user closes the browser (cached for a single session).</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
expires in<br><small>(eg. 1 Month 5 Days)</small>                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Value will be cached for a given time. Value can store multiple time units in singular or plural form:<br>
%n (year|week|day|hour|second)/s</p>
</span></li><li class="shortcode-doc__param-value">
<strong>
max                                            </strong>
<span class="shortcode-doc__param-value-desc"><p>Value will be cached for as long as the user does not clear the browser cache.</p>
</span></li></ul>`,
					type: 'select',
					options: [
						{ value: '', text: 'Don\'t keep' },
						{ value: 'session', text: 'Session' },
						{ value: 'custom', text: 'Custom' },
						{ value: 'max', text: 'Max Possible' }
					],
					selected: '',
					custom: '3 Months'
				},
				default: {
					value: '',
					info: `<div>
<strong>default | optional</strong></div><div><p>Defines the default value if shortcodes result in an empty string.</p></div>`,
				},
				test: {
					value: '',
					info: '',
				}
			}




		}
	},
	watch: {

	},
	methods: {
		setInfo(param) {
			if (this.info.lock) return false;
			this.info.field = param;
		},
		clearInfo() {
			if (this.info.lock) return false;
			this.info.field = null;
		},
		lockInfo(param) {
			this.info.lock = true;
			this.info.field = param;
		},
		unlockInfo() {
			this.info.lock = false;
		},
		getInfo() {
			if (this.info.field) {
				return this.fields[this.info.field].info;
			} else {
				return '<div>Hover or select field to get more info.</div>';
			}
		},
		copyShortcode() {
			document.querySelector('.shortcode-textarea').select();
			document.querySelector('.shortcode-textarea').setSelectionRange(0, document.querySelector('.shortcode-textarea').value.length);
			document.execCommand('copy');
			document.querySelector('.shortcode-textarea-wrapper-button').focus()
			this.copied = true;
			setTimeout(() => {
				this.copied = false;
			}, 3000);
		},
		getParam() {
			if (this.fields.param.value == '') {
				return 'param="your_param"';
			}
			return 'param="' + this.fields.param.value + '"';
		},
		getStyle() {
			if (this.fields.style.selected == '') {
				return '';
			}
			return ' style="' + this.fields.style.selected + '"';
		},
		getPrefix() {
			if (this.fields.prefix.value == '') {
				return '';
			}
			return ' prefix="' + this.fields.prefix.value + '"';
		},
		getSufix() {
			if (this.fields.sufix.value == '') {
				return '';
			}
			return ' sufix="' + this.fields.sufix.value + '"';
		},
		getDefault() {
			if (this.fields.default.value == '') {
				return '';
			}
			return ' default="' + this.fields.default.value + '"';
		},
		getCache() {
			if (this.fields.cache.value == '') {
				return '';
			}
			return ' cache="false"';
		},
		getExpires() {
			if (this.fields.expires.selected == '') {
				return '';
			}
			if (this.fields.expires.selected == 'custom') {
				return ' keep="' + this.fields.expires.custom + '"';
			}
			return ' keep="' + this.fields.expires.selected + '"';
		},
		shortcode() {
			return '[dki4wp ' + this.getParam() + '' + this.getStyle() + '' + this.getPrefix() + '' + this.getSufix() + '' + this.getDefault() + '' + this.getCache() + '' + this.getExpires() + ']';
		},
		evaluate() {
			let value = this.fields.test.value ? this.fields.prefix.value + this.fields.test.value + this.fields.sufix.value : this.fields.default.value ? this.fields.default.value : '';
			if (this.fields.style.selected == 'upper') {
				value = value.toUpperCase();
			}
			if (this.fields.style.selected == 'lower') {
				value = value.toLowerCase();
			}
			if (this.fields.style.selected == 'sentence') {
				if (value) {
					value = value[0].toUpperCase() + value.slice(1);
				}
			}
			if (this.fields.style.selected == 'title') {
				if (value) {
					const words = value.split(" ");

					for (let i = 0; i < words.length; i++) {
						words[i] = words[i][0].toUpperCase() + words[i].substr(1);
					}

					value = words.join(" ");
				}
			}
			return value;
		}
	},
	created() {



	},
	mounted() {

	},
	setup() {
		return {

		}
	}
}
</script>

<style>	
[data-generator] {
	margin-top: 0.5em;
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
	font-size: 14px;
}
* {
	box-sizing: border-box;
}
.shortcode-evaluate-wrapper {
	
	background: #efefef;
	border-radius: 0.5em;
	padding: 0.5em 1em;
	line-height: 1.25;
}
.shortcode-textarea-wrapper {
	position: relative;
	
}
.shortcode-textarea-wrapper-button {
	position: absolute;
	top: 0;
	right: 0;
	border-radius: 0.5em;
	border: 0;
	padding: 0;
	margin: 0;
	padding: 0.5em;
	cursor: pointer;
	width: 42px;
	height: 42px;
	color: white;
	background: #424495;
	svg {
		width: 1em;
		height: 1em;
		fill: currentColor;
	}
}
.shortcode-textarea-wrapper-button__tooltip {

	position: absolute;
         top: 100%;
         left: 50%;
         background: #efefef;
         white-space: nowrap;
         transform: translate(-50%, 0.5em);
         padding: 0.5em;
         border-radius: 0.5em;
         color: #000;
         box-shadow: 0 3px 10px 3px rgba(0,0,0,0.1);
         opacity: 0;
         transition: 0.125s ease-in-out all;
         z-index: 10;
         pointer-events: none;
}
.shortcode-textarea-wrapper-button:hover {
		background: rgb(0, 0, 108);
	}
	.shortcode-textarea-wrapper-button:hover .shortcode-textarea-wrapper-button__tooltip {
		opacity: 1;
	}
	.shortcode-textarea {
	resize: none;
	width: 100%;
	
	background: #efefef;
	border-radius: 0.5em;
	padding: 0.5em calc(42px + 1em) 0.5em 1em;
	line-height: 1.25;
	height: 5em;
	border: none;
	color: #000;
}
.dki4wp-generator__fields-columns {
	display: flex;
	gap: 15px;
}
.dki4wp-generator__fields {
	width: calc(66.666666%);
	display: flex;
	flex-wrap: wrap;
	gap: 15px;
	margin-bottom: 15px; 
	padding-bottom: 15px;
	border-bottom: 1px solid #efefef;
}
.dki4wp-generator__info {
	width: calc(33.333333%);
	padding-top: 33px;
}

.dki4wp-generator__info label {
		width: 100%;
		height: 100%;
	}
.dki4wp-generator__info-panel {
	height: 100%;
	max-height: 438px;
	background: white;
	border: 1px solid #efefef;
	border-radius: 0.5em;
	overflow-x: hidden;
	padding: 15px;
	overflow-y: scroll;
}
.dki4wp-generator__info-panel :is(span, p) {
	margin: 0;
	padding: 0;
}
.dki4wp-generator__fields:not(:first-child) {
	margin-top: 15px;
	padding-top: 15px;
	border-top: 1px solid #efefef;
}
.dki4wp-generator__pseuo-label {
	
			font-size: 0.9em;
			padding-left: 5px;
			margin-bottom: 5px;
}
.dki4wp-generator__label {
		width: calc(50% - 7.5px);
		display: flex;
		gap: 5px;
		flex-direction: column;
		span {
			display: flex;
			gap: 10px;
			font-size: 0.9em;
			padding-left: 5px;
		}
		input[type="text"],
		select {
			display: block;
			width: auto;
			padding: 0.5em 1em;
			border: 1px solid #efefef;
			background-color: #efefef;
			border-radius: 0.5em;
			align-self: normal;
			height: 42px;
		}
		input[type="checkbox"] {
			display: none;
		}
		input[type="checkbox"] + .checkbox-visible {
			display: block;
			margin-top: 6px;
			width: 52px;
			height: 30px;
			background: #efefef;
			border-radius: 15px;
			padding: 4px;
			cursor: pointer;
			box-sizing: border-box;
		}
		input[type="checkbox"] + .checkbox-visible::before {
			content: '';
			display: block;
			width: 22px;
			height: 22px;
			border-radius: 50%;
			background: #424495;
		}
		input[type="checkbox"]:checked + .checkbox-visible::before {
			transform: translateX(100%);	
		}
	}
	.dki4wp-generator__fields-title {
		width: 100%;
		font-weight: bold;
		opacity: 0.5;
	}
	.dki4wp-generator__label--full-width {
		width: 100%;
	}

</style>