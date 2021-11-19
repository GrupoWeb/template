<template>
  <div>
    <CCard>
      <CCardHeader>
        Expedientes
      </CCardHeader>
      <CCardBody>
        <CDataTable
            hover
            striped
            border
            :fields="fiedlObject"
            :items="items"
            :items-per-page="8"
            pagination
            caption="Listado de Expedientes"
            :noItemsView="{ noResults: 'no filtering results available', noItems: 'Sin datos' }"
        >
          <template #Consulta="{item}">
            <td>
              <div>
                <CButtonGroup>
                  <CButton color="success" @click="showHistoryLegal(item.Expediente)">Historial</CButton>
                  <CButton color="primary" @click="EventoModal = true">Contrato</CButton>
                  <CButton color="danger" @click="showUser( item.id )">Finalizar</CButton>
                  <CButton color="warning" @click="TransferModal = true">Transferir</CButton>
                </CButtonGroup>
              </div>
            </td>
          </template>
        </CDataTable>
      </CCardBody>
    </CCard>
<!--    Modales-->
    <CModal
        :title="Modals.ModalHistory.title"
        :color="Modals.ModalHistory.color"
        :show.sync="HistorialModal"
        :size="Modals.ModalHistory.size"
        :closeOnBackdrop="Modals.ModalHistory.close"

    >
      <CCard>
        <CCardHeader><CIcon name="cilArrowRight"/> Juridico</CCardHeader>
        <CCardBody>
          <CForm>
            <CTextarea
                label="Seguimiento:"
                placeholder="Contenido..."
                rows="5"
            />
            <CSelect
                label="Evento:"
                :options="optionsEvent"
                placeholder="Seleccione..."
            />
            <CCardFooter>
              <CButton  size="sm" color="success"><CIcon name="cil-check-circle"/> Agregar</CButton>
            </CCardFooter>
          </CForm>
          <CDataTable
              hover
              striped
              border
              :fields="fieldsLegal"
              :items="itemsLegal"
              :items-per-page="8"
              pagination
              caption="Listado de Expedientes"
              :noItemsView="{ noResults: 'no filtering results available', noItems: 'Sin datos' }"
          >
            <template #Consulta="{item}">
              <td>
                <div>
                  <CButtonGroup>
                    <CButton color="success" @click="showHistoryLegal(item.Expediente)">Historial</CButton>
                    <CButton color="primary" @click="EventoModal = true">Evento</CButton>
                    <CButton color="danger" @click="showUser( item.id )">Finalizar</CButton>
                    <CButton color="warning" @click="TransferModal = true">Transferir</CButton>
                  </CButtonGroup>
                </div>
              </td>
            </template>
          </CDataTable>
        </CCardBody>
      </CCard>

      <template #footer>
        <CButton @click="closeModal('history')" color="danger">Cerrar</CButton>
      </template>
    </CModal>
    <CModal
        title="Modal title"
        color="info"
        :show.sync="EventoModal"
    >
        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
        consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
        cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
        proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </CModal>
    <CModal
        title="Modal title"
        color="info"
        :show.sync="TransferModal"
    >
      Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
      tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
      quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
      consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
      cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
      proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </CModal>
  </div>
</template>

<script>
import CTableWrapper from './components/Table';
import axios from 'axios'
import api_router from "../../router/router_path";
export default {
  name: "Expediente",
  components: { CTableWrapper },
  data() {
    return {
      items:[],
      itemsLegal: [],
      fieldsLegal:[
        {
          key: 'Fecha',
          _style: ['width:10%; text-align: center;']
        },
        {
          key: 'evento',
          label: 'Evento',
          _style: ['width:10%; text-align: center;']
        },
        {
          key: 'observaciones',
          label: 'Observaciones',
          _style: ['width:10%; text-align: center;']
        },
        {
          key: 'nombre',
          label: 'Empleado',
          _style: ['width:10%; text-align: center;']
        },
      ],
      fiedlObject: [
        {
          key: 'Expediente',
          label: 'Número Expediente',
          _style: ['width:10%']
        },
        {
          key: 'Procedencia',
          _style: ['width:10%']
        },
        {
          key: 'Fecha',
          _style: ['width:10%; text-align: center;']
        },
        {
          key: 'Descripción',
          _style: ['width:50%']
        },
        {
          key: 'Consulta',
          _style: ['width:20%']
        },
      ],
      HistorialModal: false,
      EventoModal: false,
      TransferModal: false,
      optionsEvent: [],
      Modals: {
        ModalHistory: {
          title: '',
          size: 'xl',
          close: false,
          color: 'info'
        }
      }
    }
  },
  mounted(){
    this.getLegalData();

  },
  methods: {
    getLegalData() {
      axios.get( this.$apiAdress + '/api/ver?token=' + localStorage.getItem("api_token"))
      .then(response => {
        this.items =  response.data;
      })
      .catch(error => {
        console.log(error)
        this.$router.push({ path: '/login' });
      })
    },
    showHistoryLegal(number){
      this.HistorialModal = true
      this.Modals.ModalHistory.title = 'Historial del Expediente No. ' + number
      axios.post(this.$apiAdress + api_router[0].historyLegal + '?token=' + localStorage.getItem("api_token"), {
        code: number
      }).then(response => {
        console.log(response.data)
        this.itemsLegal = response.data
      })
      this.getEvent();
    },
    closeModal(modal){
      if( modal === 'history') {
        this.HistorialModal = false
      }
    },
    getEvent(){
      axios.get(this.$apiAdress + api_router[0].getEvent + '?token=' + localStorage.getItem("api_token"))
      .then(response => {
        response.data.forEach(elements => {
          this.optionsEvent.push(
            {
              value: elements.id_jevento,
              label: elements.evento
            }
          )
        })
        console.log(this.optionsEvent)
      })
    }
  }
}
</script>

<style scoped>
</style>
