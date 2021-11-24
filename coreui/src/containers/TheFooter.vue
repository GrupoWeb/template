<template>
  <CFooter :fixed="false">
    <div>
      <a href="https://www.diaco.gob.gt/" target="_blank">DIACO</a>
      <span class="ml-1">&copy; {{new Date().getFullYear()}} Ministerio de Economía.</span>
    </div>
    <div class="ml-auto">
      <span class="mr-1">Desarrollado por</span>
      <a href="https://www.mineco.gob.gt/" target="_blank">Dirección de Tecnologías de la Información</a>
    </div>
  </CFooter>
</template>

<script>
export default {
  name: 'TheFooter'
}
</script>
