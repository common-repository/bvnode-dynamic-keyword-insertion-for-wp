<template>
<div v-if="!Object.keys(sets).length" class="no-sets">
			No sets defined.
		</div>
		<div v-for="(set, index) in sets" :key="index" class="set">
			
			<div class="set__header">

				<div class="set__name">
					<label>Name:</label>
					<input v-model="set.name" placeholder="Set Name" @keypress.enter.prevent  />
					<span class="set__name-id">ID: [{{index}}]</span>
				</div>
				
				<button @click.prevent="deleteSet($event, index)"  @mouseleave="resetParamButton($event)"  class="button-bvnode">
				<svg class="button-bvnode__icon button-bvnode__icon--confirm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M80 160c0-35.3 28.7-64 64-64h32c35.3 0 64 28.7 64 64v3.6c0 21.8-11.1 42.1-29.4 53.8l-42.2 27.1c-25.2 16.2-40.4 44.1-40.4 74V320c0 17.7 14.3 32 32 32s32-14.3 32-32v-1.4c0-8.2 4.2-15.8 11-20.2l42.2-27.1c36.6-23.6 58.8-64.1 58.8-107.7V160c0-70.7-57.3-128-128-128H144C73.3 32 16 89.3 16 160c0 17.7 14.3 32 32 32s32-14.3 32-32zm80 320a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>
					<svg class="button-bvnode__icon button-bvnode__icon--default"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/></svg>
					<span class="button-bvnode__tooltip button-bvnode__tooltip--confirm">Are you sure?</span>
					<span class="button-bvnode__tooltip button-bvnode__tooltip--default">Remove <strong>{{ set.name }}</strong> Set</span>
				</button>

				<button @click.prevent="cloneSet(set)" class="button-bvnode" :class="{'button-bvnode--disabled': this.perms.sets <= Object.keys(sets).length}">
					<svg class="button-bvnode__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M288 448H64V224h64V160H64c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H288c35.3 0 64-28.7 64-64V384H288v64zm-64-96H448c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H224c-35.3 0-64 28.7-64 64V288c0 35.3 28.7 64 64 64z"/></svg>
					<span class="button-bvnode__tooltip">Clone <strong>{{ set.name }}</strong> Set</span>
				</button>

			</div>
		
		<div v-if="!Object.keys(set.params).length" class="no-params">
			No params defined.
		</div>

		<div class="set__params" v-if="Object.keys(set.params).length">
			<div class="set__params-header">
				<div>Param</div>
				<div>Value</div>
			</div>
			<div v-for="(param, param_index) in set.params" :key="param_index" class="set__param">
				<input v-model="param.name"  placeholder="Param Name" @keypress.enter.prevent />
				<input v-model="param.value" placeholder="Param Value" @keypress.enter.prevent />
				<button @click.prevent="deleteParm($event, index, param_index)"  @mouseleave="resetParamButton($event)" class="button-bvnode">
					<svg class="button-bvnode__icon button-bvnode__icon--confirm" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M80 160c0-35.3 28.7-64 64-64h32c35.3 0 64 28.7 64 64v3.6c0 21.8-11.1 42.1-29.4 53.8l-42.2 27.1c-25.2 16.2-40.4 44.1-40.4 74V320c0 17.7 14.3 32 32 32s32-14.3 32-32v-1.4c0-8.2 4.2-15.8 11-20.2l42.2-27.1c36.6-23.6 58.8-64.1 58.8-107.7V160c0-70.7-57.3-128-128-128H144C73.3 32 16 89.3 16 160c0 17.7 14.3 32 32 32s32-14.3 32-32zm80 320a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"/></svg>
					<svg class="button-bvnode__icon button-bvnode__icon--default"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M432 256c0 17.7-14.3 32-32 32L48 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l352 0c17.7 0 32 14.3 32 32z"/></svg>
					<span class="button-bvnode__tooltip button-bvnode__tooltip--confirm">Are you sure?</span>
					<span class="button-bvnode__tooltip button-bvnode__tooltip--default">Remove <strong>{{ param.name }}</strong> Param</span>
				</button>
			</div>
		
		</div>

		<button @click.prevent="addParam(index)" class="button-bvnode button-bvnode--to-right" v-if="this.perms.params > Object.keys(set.params).length">
			<svg class="button-bvnode__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg> 
			<span>Add Param</span>
		</button>

		</div>

		<button @click.prevent="addSet" class="button-bvnode button-bvnode--to-right button-bvnode--last" v-if="this.perms.sets > Object.keys(sets).length">
			<svg class="button-bvnode__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/></svg> 
			<span>Add Set</span>
		</button>


		
<button type="submit" class="button-bvnode" :class="{'button-bvnode--disabled': this.pristine }" >
<svg class="button-bvnode__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
		<span class="button-bvnode__text">Save</span>
	</button>
				
</template>

<script>
  
  export default {
    name: 'BVNodeSets',
    props: ['config', 'setsValue'],
    
		data() {
			return {
				loaded: false,
				submit: false,
				pristine: true,
				sets: [],
				newSetsValue: ''
			}
		},
		watch: {
			sets: {
				handler: function(newValue) {
				if(!this.loaded) return
					this.newSetsValue = JSON.stringify(newValue);
					this.pristine = false;
				},
				deep: true
			}
		},
		methods: {
			deleteParm($event, set, param) {
				if ($event.currentTarget.classList.contains('button-bvnode--confirm')) {
					delete this.sets[set].params[param];
				} else {
					
					$event.currentTarget.classList.add('button-bvnode--confirm');
				}
			},
			resetParamButton($event) {
				if (!$event.currentTarget.classList.contains('button-bvnode--confirm')) return;
				$event.currentTarget.classList.remove('button-bvnode--confirm');
			},
			addParam(set) {
				let object = {};
				const setIndex = Object.keys(this.sets[set].params).length ? (parseInt(Object.keys(this.sets[set].params)[Object.keys(this.sets[set].params).length-1].replace('param', '')) + 1) : 0;
				object['param' + setIndex] = {
					name: '',
					value: ''
				};
				this.sets[set].params = Object.assign( this.sets[set].params, object)
			},
			deleteSet($event, set) {
				
				if ($event.currentTarget.classList.contains('button-bvnode--confirm')) {
					delete this.sets[set];
				} else {
					
					$event.currentTarget.classList.add('button-bvnode--confirm');
				}
			},
			cloneSet(set) {
				let object = {};
				const setIndex = Object.keys(this.sets).length ? (parseInt(Object.keys(this.sets)[Object.keys(this.sets).length-1].replace('set', '')) + 1) : 0;
				object['set' + setIndex] = JSON.parse(JSON.stringify(set));
				this.sets = Object.assign( this.sets, object);
			},
			addSet() {
				let object = {};
				const setIndex = Object.keys(this.sets).length ? (parseInt(Object.keys(this.sets)[Object.keys(this.sets).length-1].replace('set', '')) + 1) : 0;
				object['set' + setIndex] = {
					name: 'New Set',
					params: {}
				};
				this.sets = Object.assign( this.sets, object)
			},
			setSubmit() {
				this.submit = true;
			},
			getSetsValue() {
				return this.newSetsValue;
			},
			notifyPristine(e) {
				
				if (this.pristine || this.submit) {
					return undefined;
				}
				
				var confirmationMessage = 'It looks like you have been editing something. '
										+ 'If you leave before saving, your changes will be lost.';

				(e || window.event).returnValue = confirmationMessage; //Gecko + IE
				return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
			}
		},
		created() {
			this.sets = JSON.parse(this.setsValue)
			this.perms = JSON.parse(atob(this.config));
			window.addEventListener("beforeunload", (e) => { this.notifyPristine(e); });
			
		},
		mounted() {
			this.loaded = true;
		},
		setup() {
		return {
			
		}
		}
  }
</script>

<style>
</style>