<template>
  <div>
    <CCard>
      <CCardHeader>
        Expedientes
      </CCardHeader>
      <CCardBody>
        <CDataTable

            striped
            border
            :table-filter='{ placeholder : "Ingrese expediente", label: "Búsqueda:" }'
            :fields="fiedlObject"
            :items="items"
            :items-per-page-select="{ label: 'Cantidad'}"
            :items-per-page="8"
            pagination
            caption="Listado de Expedientes"
            :noItemsView="{ noResults: 'no filtering results available', noItems: 'Sin datos' }"
        >
          <template #Consulta="{item}">
            <td>
              <div>
                <CButtonGroup>
                  <CButton color="success" @click="showHistoryLegal(item.Expediente, item.Procedencia, item.id_jevento, item.id_texpj)">Historial</CButton>
                  <CButton color="primary" @click="getInfo(item.Expediente)">Contrato</CButton>
                  <CButton color="warning" @click="transferData(item.Expediente, item.Procedencia, item.id_jevento, item.id_texpj)">Transferir</CButton>
                  <CButton color="danger"  @click="fileData(item.Expediente, item.Procedencia, item.id_jevento, item.id_texpj)">Archivar</CButton>
                  <CButton color="success" @click="showUser( item.id )">Aprobar</CButton>

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
        <CCardBody>
          <CForm>
            <CTextarea
                label="Seguimiento:"
                placeholder="Contenido..."
                rows="5"
                v-model="Modals.ModalHistory.data.observacion"
            />
            <CSelect
                label="Evento:"
                :options="optionsEvent"
                placeholder="Seleccione..."
                :value.sync="Modals.ModalHistory.data.select"
            />
            <CCardFooter>
              <CButton  size="sm" color="success" @click="setComment"><CIcon name="cil-check-circle"/> Agregar</CButton>
            </CCardFooter>
          </CForm>
          <CDataTable
              hover
              striped
              border
              small
              :fields="fieldsLegal"
              :items="itemsLegal"
              :items-per-page="4"
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
        :title="Modals.ModalContrato.title"
        :color="Modals.ModalContrato.color"
        :show.sync="EventoModal"
        :closeOnBackdrop="Modals.ModalContrato.close"

    >
      <div v-html="Modals.ModalContrato.template"></div>
      <template #footer>
        <CButton @click="closeModal('contrato')" color="danger">Cerrar</CButton>
      </template>
    </CModal>

    <CModal
        :title="Modals.ModalTransfer.title"
        :color="Modals.ModalTransfer.color"
        :show.sync="TransferModal"
        :size="Modals.ModalTransfer.size"
        :closeOnBackdrop="Modals.ModalTransfer.close"
    >
      <CForm>
        <CTextarea
            label="Observación:"
            placeholder="Contenido..."
            rows="5"
            v-model="Modals.ModalTransfer.data.observacion"
        />
        <CSelect
            label="Usuario:"
            :options="Modals.ModalTransfer.optionUser"
            placeholder="Seleccione..."
            :value.sync="Modals.ModalTransfer.data.user"
        />
        <CSelect
            label="Evento:"
            :options="optionsEvent"
            placeholder="Seleccione..."
            :value.sync="Modals.ModalTransfer.data.select"
        />
      </CForm>
      <template #footer>
        <CButton @click="closeModal('Transfer')" color="danger">Cerrar</CButton>
        <CButton @click="setTransfer()" color="success">Trasladar</CButton>
      </template>
    </CModal>

    <CModal
        :title="Modals.ModalArchivar.title"
        :color="Modals.ModalArchivar.color"
        :show.sync="ArhivarFile"
        :size="Modals.ModalArchivar.size"
        :closeOnBackdrop="Modals.ModalArchivar.close"
    >
      <CForm>
        <CTextarea
            label="Observación:"
            placeholder="Contenido..."
            rows="5"
            v-model="Modals.ModalArchivar.data.observacion"
        />

      </CForm>
      <template #footer>
        <CButton @click="closeModal('Transfer')" color="danger">Cerrar</CButton>
        <CButton @click="setTransfer()" color="success">Archivar</CButton>
      </template>
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
      token: '?token=' + localStorage.getItem("api_token"),
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
      ArhivarFile: false,
      optionsEvent: [],
      Modals: {
        ModalHistory: {
          title: '',
          size: 'xl',
          close: false,
          color: 'info',
          data: {
            expediente: '',
            procedencia: '',
            evento: '',
            text: '',
            observacion: '',
            select: ''
          }
        },
        ModalContrato: {
          title: '',
          size: 'sm',
          close: false,
          color: 'info',
          template: '',
          data: {
            expediente: '',
            procedencia: '',
            evento: '',
            text: '',
            observacion: '',
            select: ''
          }
        },
        ModalTransfer: {
          title: '',
          size: 'sm',
          close: false,
          color: 'primary',
          template: '',
          optionEvent: [],
          optionUser: [],
          data: {
            expediente: '',
            procedencia: '',
            evento: '',
            text: '',
            observacion: '',
            select: '',
            user: ''
          }
        },
        ModalArchivar: {
          title: '',
          size: 'lg',
          close: false,
          color: 'danger',
          template: '',
          optionEvent: [],
          optionUser: [],
          data: {
            expediente: '',
            procedencia: '',
            evento: '',
            text: '',
            observacion: '',
            select: '',
            user: ''
          }
        },
      }
    }
  },
  mounted(){
    this.getLegalData();

  },
  methods: {
    getLegalData() {
      axios.get( this.$apiAdress + '/api/ver'+ this.token)
      .then(response => {
        this.items =  response.data;
      })
      .catch(error => {
        this.$router.push({ path: '/login' });
      })
    },
    showHistoryLegal(number, precedent, event, text){
      this.HistorialModal = true
      this.Modals.ModalHistory.title = 'Historial del Expediente No. ' + number
      this.Modals.ModalHistory.data.expediente = number
      this.Modals.ModalHistory.data.procedencia = precedent
      this.Modals.ModalHistory.data.evento = event
      this.Modals.ModalHistory.data.text = text
      this.getHistoryData(number);
      this.getEvent();
    },
    closeModal(modal){
      if( modal === 'history') {
        this.HistorialModal = false
        this.optionsEvent = []
      }else if( modal === 'contrato') {
        this.EventoModal = false
      }else if ( modal === 'Transfer') {
        this.TransferModal = false
        this.optionsEvent = []
        this.Modals.ModalTransfer.optionUser = []
      }
    },
    getHistoryData(number){
      axios.post(this.$apiAdress + api_router[0].historyLegal + this.token, {
        code: number
      }).then(response => {
        this.itemsLegal = response.data
      })
    },
    getEvent(){
      axios.get(this.$apiAdress + api_router[0].getEvent + this.token)
      .then(response => {
        response.data.forEach(elements => {
          this.optionsEvent.push(
            {
              value: elements.id_jevento,
              label: elements.evento
            }
          )
        })
      })
    },
    getUserLegal(data_array){
      axios.get(this.$apiAdress + api_router[0].getUserLegal + this.token)
      .then(response => {
        response.data.forEach(elements => {
          data_array.push(
            {
              value: elements.code,
              label: elements.user_name
            }
          )
        })
      })
    },
    reset(modal){

      if (modal === 1){
        this.Modals.ModalHistory.data.select = ''
        this.Modals.ModalHistory.data.observacion = ''
      }else if (modal === 3) {
        this.Modals.ModalTransfer.data.user = ''
        this.Modals.ModalTransfer.data.select = ''
        this.Modals.ModalTransfer.data.observacion = ''
      }


    },
    getInfo(contrato){
      this.EventoModal = true
      this.Modals.ModalContrato.title = 'Datos del contrato No. ' + contrato
      axios.post(this.$apiAdress + api_router[0].getInfoByCode + this.token, {
        code: contrato
      })
      .then(response => {
        const { registro, nempresa, patente, fecha, rlegal, ncomercial, nombre_deptos, nombre_r, direccion, zona, colonia, nit, municipio, telefono1, fax, email, usuario, f63a  } = response.data[0]
        this.Modals.ModalContrato.template= `  <table class="table table-striped table-bordered table-sm">
        <tr>
          <td class="font-weight-bold">Registro: </td>
          <td>${ registro }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Nombre de la Empresa: </td>
          <td>${ nempresa }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Número de Patente: </td>
          <td>${ patente }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Fecha: </td>
          <td>${ fecha }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Representante Legal: </td>
          <td>${ rlegal }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Nombre Comercial: </td>
          <td>${ ncomercial }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Departamento: </td>
          <td>${ nombre_deptos }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Sede Regional: </td>
          <td>${ nombre_r }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Dirección: </td>
          <td>${ direccion }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Zona: </td>
          <td>${ zona }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Colonia: </td>
          <td>${ colonia }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Nit: </td>
          <td>${ nit }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Municipio: </td>
          <td>${ municipio }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Teléfono: </td>
          <td>${ telefono1 }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Fax: </td>
          <td>${ fax }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Correo Electrónico: </td>
          <td>${ email }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Usuario: </td>
          <td>${ usuario }</td>
        </tr>
        <tr>
          <td class="font-weight-bold">Formulario 63A: </td>
          <td>${ f63a }</td>
        </tr>
      </table>`;

      })
    },
    setTransfer(){
      axios.post(this.$apiAdress + api_router[0].setEvent + this.token, {
        expediente: this.Modals.ModalTransfer.data.expediente,
        evento: this.Modals.ModalTransfer.data.select,
        observaciones: this.Modals.ModalTransfer.data.observacion,
        comercial: this.Modals.ModalTransfer.data.procedencia,
        user_id: this.Modals.ModalTransfer.data.user,
        flag: true
      })
      .then(response => {
        if (response.status === 202){
          this.reset(3)
          this.closeModal('Transfer')
          this.getLegalData();
          this.$swal({
            icon: "success",
            title: "Trasladado",
            showConfirmButton: false,
            timer: 2500,
          });
        }else{
          this.reset(3)
          this.closeModal('Transfer')
          this.$swal({
            icon: "error",
            title: "Error de servidor",
            showConfirmButton: false,
            timer: 2500,
          });
        }


      })

    },
    transferData(contrato, precedent, event, text){
      this.TransferModal = true
      this.Modals.ModalTransfer.title = 'Expediente No. ' + contrato
      this.Modals.ModalTransfer.data.expediente = contrato
      this.Modals.ModalTransfer.data.procedencia = precedent
      this.Modals.ModalTransfer.data.evento = event
      this.Modals.ModalTransfer.data.text = text
      this.getEvent(this.Modals.ModalTransfer.optionEvent);
      this.getUserLegal(this.Modals.ModalTransfer.optionUser)
    },
    fileData(contrato, precedent, event, text){
      this.ArhivarFile = true
      this.Modals.ModalArchivar.title = 'Archivar Expediente No. ' + contrato + '  /  ' + precedent
      this.Modals.ModalArchivar.data.expediente = contrato
      this.Modals.ModalArchivar.data.procedencia = precedent
      this.Modals.ModalArchivar.data.evento = event
      this.Modals.ModalArchivar.data.text = text
      this.getEvent(this.Modals.ModalArchivar.optionEvent);
      this.getUserLegal(this.Modals.ModalArchivar.optionUser)
    },
    setComment(){
      axios.post(this.$apiAdress + api_router[0].setEvent + this.token, {
        expediente: this.Modals.ModalTransfer.data.expediente,
        evento: this.Modals.ModalTransfer.data.select,
        observaciones: this.Modals.ModalTransfer.data.observacion,
        comercial: this.Modals.ModalTransfer.data.procedencia,
        flag: null
      })
      .then(response => {

        if (response.status === 202){
          this.getHistoryData(this.Modals.ModalHistory.data.expediente);
          this.$swal({
            icon: "success",
            title: "Agregado",
            showConfirmButton: false,
            timer: 2500,
          });
          this.reset(1)
        }else{
          this.$swal({
            icon: "error",
            title: "Error de servidor",
            showConfirmButton: false,
            timer: 2500,
          });
        }


      })
    }
  }
}
</script>

<style scoped>
</style>
