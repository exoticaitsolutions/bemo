
<template>
  <div class="board-container">
    <div class="board">

      <div class="clear-button">
        <ui-button :disabled="cardList.length === 0" @click="reset">Reset</ui-button>
      </div>

      <div class="lists-container">

        <Container
          lock-axis="x"
          orientation="horizontal"
          drag-handle-selector=".list-drag-handle"
          @drop="onListDrop"
        >
  

          <Draggable class="drag-item" v-for="(list, listIndex) in cardList" :key="list.id">
          <button @click="deleteCard(list.id)">Delete</button>
            <section class="list-container" ref="list" :data-id="list.id">

              <div class="list-header">
                <span class="list-drag-handle">&#x2630;</span>
                {{ list.title }}
              </div>

              <Container
                group-name="list"
                drag-class="card-ghost"
                drop-class="card-ghost-drop"
                non-drag-area-selector=".icon"
                :animation-duration="100"
                @drop="e => onCardDrop(e, list, listIndex)"
              >
                <Draggable v-for="item in list.items" :key="item.id">
                  <Card :item="item" @edit="editItem" @delete="deleteItem"/>
                </Draggable>

              </Container>

              <div class="item-entry">
                <ui-item-entry :list-id="list.id"
                               placeholder="Add a Card"
                               icon="ellipsis-h"
                               @click="onAddItem"/>
              </div>

            </section>

          </Draggable>
  

        </Container>

        <div class="new-list">
          <ui-item-entry placeholder="Add a Column" @click="onAddList" />

        </div>
      </div>

    </div>

    <ui-modal ref="modal"
              :active="modal"
              :cancellable="1"
              @close="hideModal"
    >
      <UiItemForm ref="form"
                  @submit="onAddFullItem"
                  @cancel="hideModal"/>
    </ui-modal>

  </div>

</template>

<script>
import Vue from 'vue'
import { Container, Draggable } from 'vue-smooth-dnd'

import Card from './Card'
import UiItemForm from '../ui/UiItemForm'
import UiItemEntry from '../ui/UiItemEntry'
import { makeDropHandler } from '../utils/plugins'

export default {
  components: {
    Container,
    Draggable,
    UiItemEntry,
    UiItemForm,
    Card,
  },

  data: function () {
    return {
      modal: false,
      activeListId: null,
      cardList : []
    }
  },

   async created() {
      await this.$axios.get('http://127.0.0.1:8000/api/card-list').then(
        (resposne) => {
          this.cardList = resposne.data.data
          console.log(resposne.data.data)
          
        }).catch( (error) => {

        })  
  },

  methods: {

  async  onAddList ({ text }) {
      await this.$axios.post('http://127.0.0.1:8000/api/add-card', {title: text}).then(
      (resposne) => {
        this.cardList.push(resposne.data.data)
        console.log(this.cardList)
        
      }).catch( (error) => {

      })
    },

    onAddItem ({ id, text, more }) {
      if (more) {
        this.activeListId = id
        this.modal = true
        this.showModal({ title: text })
        
        return
      }
      this.addItem(id, text)
    },


    onAddFullItem (item) {
      item.id
        ? this.$store.commit('updateItem', { itemId: item.id, ...item })
        : this.addItem(this.activeListId, item.title, item.description, item.date)
      this.hideModal()
    },

   async addItem (listId, title, description, date) {
      await this.$axios.post('http://127.0.0.1:8000/api/add-item', {title: title, listId: listId}).then(
      (resposne) => {
          const index = this.cardList.findIndex(list => list.id === listId)
          this.cardList[index].items.push(resposne.data.data)
      }).catch( (error) => {

      })
    },

    async deleteItem(item){
        await  this.$axios.delete('http://127.0.0.1:8000/api/delete-item/' + item.id)
        .then( (response) => {
              const index = this.cardList.findIndex(list => list.id === item.cards_id)
              const itemindex = this.cardList[index].items.findIndex(k => k.id === item.id)
              this.cardList[index].items.splice(itemindex, 1)
        }).catch( (error) => {
          
        });
        },

         async deleteCard(id){
          alert('gggg')
        await  this.$axios.delete('http://127.0.0.1:8000/api/delete-card/' + id)
        .then( (response) => {
              const index = this.cardList.findIndex(list => list.id === id)
              this.cardList.splice(index,1)
        }).catch( (error) => {
          
        });
     
    },
    editItem (item) {
      this.showModal(item)
    },

    onListDrop: makeDropHandler('onListDropComplete'),

    onListDropComplete: function (src, trg) {
      this.$store.commit('moveList', [src.index, trg.index])
    },

    onCardDrop: makeDropHandler('onCardDropComplete'),

    onCardDropComplete (src, trg, element, payload) {
      this.$store.commit('moveItem', [
        src.params[1],
        src.index,
        trg.params[1],
        trg.index,
      ])
    },

    showModal (item) {
      this.modal = true
      this.$nextTick(() => {
        this.$refs.form.show(item)
      })
    },

    hideModal () {
      this.focusInput(this.activeListId)
      this.modal = false
    },

    focusInput (listId) {
      const index = this.lists.findIndex(list => list.id === listId)
      if (index > -1) {
        this.$refs.list[index].querySelector('input').focus()
      }
    },

    reset () {
      if (confirm('Are you sure you want to reset the board?')) {
        this.$store.commit('reset')
      }
    },
  }
}
</script>

<style lang="scss">
  .board {
    margin-top: 20px;
    white-space: nowrap;
    > * {
      display: inline-block;
    }

    .new-list {
      margin-top: 10px;
    }
  }

  $column-width: 320px;

  .list-container {
    width: $column-width;
    padding: 10px;
    margin: 5px;
    margin-right: 20px;
    background-color: #f3f3f3;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.24);
  }

  .lists-container {
    > * {
      display: inline-block;
      vertical-align: top;
    }
  }

  .list-header {
    margin-bottom: 5px;
  }

  .card {
    margin: 5px;
    background-color: white;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.24);
    padding: 10px;
  }

  .card-ghost {
    transition: 0.25s all;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.12);
    transform: scale(1.1);
  }

  .card-ghost-drop {
    transform: scale(1);
  }

  .list-header {
    font-size: 18px;
  }

  .list-drag-handle {
    cursor: move;
    padding: 5px;
  }

  .item-entry {
    padding-top: 10px;
    margin-top: 10px;
    border-top: 1px solid #DDD;
  }

  .new-list {
    width: $column-width;
    margin-left: -10px;
  }

  .clear-button {
    position: absolute;
    top: 20px;
    right: 20px;
  }
  .smooth-dnd-container.horizontal {
    display: flex;
}
</style>