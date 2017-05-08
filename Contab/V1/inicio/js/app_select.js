function iniciaSelect(){
  $(".select2_single").select2({
    placeholder: "Seleccione una opción",
    allowClear: false
  });
  $(".select2_group").select2({});
  $(".select2_multiple").select2({
  placeholder: "Seleccione uno o más",
    allowClear: true
  });
}