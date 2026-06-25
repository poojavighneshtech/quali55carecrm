$(".edit-product").on("click",function(){
    // alert($(this).data("order_details_id"));
    editProduct_fetch_details();
    $("#modal_update_product_details").modal("show");
});


// Crud Operations and fetch data from current actions....
$('input[name="update_product_type"]').on('change', function(){
    let product_type = $(this).val();
    if(product_type == $(".edit-product").data("product_type"))
    {
        editProduct_fetch_details();
    }
    else
    {
        
    }
})
function editProduct_fetch_details() 
{
    let order_details_id = $(".edit-product").data("order_details_id");
    let product_type = $(".edit-product").data("product_type");
    let dataString = ({_token:"{{ csrf_token() }}",order_details_id:""+order_details_id,product_type:""+product_type,request_type:"fetch-order-product"});
    $.ajax({
        type: "POST",
        url: "{{url('/')}}/updateOrderProduct",
        data: dataString,
        cache:false,
        success: function (data)
        {
            let vendor_details = data['vendor_details'];
            let order_details = data['order_details'];
            let warehouse_details = data['warehouse_details'];
            let brand_details = data['brand_details'];
            let batch_details = data['batch_details'];
            let product_type = order_details[0].sale_rental;
            if(product_type == "Rental")
            {
                // $('radio[name=update_product_type]').val("Rental");
                $("#rental").prop('checked', true);
            }
            else
            {
                $("#sale").prop('checked', true);
            }
            $("#update_product_rent").val(order_details[0].product_rent);
            $("#update_product_deposite").val(order_details[0].product_deposite);
            $("#update_transport").val(order_details[0].transport);
            //------- Vendor Selection --------//
            $("#update_select_vendor")
                .find("option")
                .remove()
                .end();
            for(var j = 0; j < vendor_details.length; j++)
            {
                
                $("#update_select_vendor").append("<option value='"+vendor_details[j].vendor_id+"'>"+vendor_details[j].vendor_name+"</option>");
            }
            $('#update_select_vendor').selectpicker('refresh');
            $('#update_select_vendor').selectpicker('val', order_details[0].vendor_id);

            //------- Warehouse Selection --------//
            $("#update_select_warehouse")
                .find("option")
                .remove()
                .end();
            for(var j = 0; j < warehouse_details.length; j++)
            {
                // console.log(warehouse_details[j].warehouse_id);
                $("#update_select_warehouse").append("<option value='"+warehouse_details[j].warehouse_id+"'>"+warehouse_details[j].wh_name+", "+warehouse_details[j].wh_area+", "+warehouse_details[j].wh_city+"</option>");
            }
            $('#update_select_warehouse').selectpicker('refresh');
            $('#update_select_warehouse').selectpicker('val', order_details[0].vendor_warehouse_id);

            //------- Brand Selection --------//
            $("#update_select_brand")
                .find("option")
                .remove()
                .end();
            for(var j = 0; j < brand_details.length; j++)
            {
                // console.log(brand_details[j].warehouse_id);
                $("#update_select_brand").append("<option value='"+brand_details[j].brand_id+"'>"+brand_details[j].brand_name+"</option>");
            }
            $('#update_select_brand').selectpicker('refresh');
            $('#update_select_brand').selectpicker('val', order_details[0].product_brand);
            if(product_type == "Rental")
            {
                $('#div_inventory_rental').show();
                $('#div_batch_rental').show();

                $('#div_inventory_sale').hide();
                $('#div_batch_sale').hide();
                //------- Batch Selection --------//
                $("#update_select_batch")
                    .find("option")
                    .remove()
                    .end();
                for(var j = 0; j < batch_details.length; j++)
                {
                    // console.log(brand_details[j].warehouse_id);
                    $("#update_select_batch").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                }
                $('#update_select_batch').selectpicker('refresh');
                $('#update_select_batch').selectpicker('val', order_details[0].vendor_product_id);

                //------- Inventory Selection --------//
                $("#update_select_inventory")
                    .find("option")
                    .remove()
                    .end();
                for(var j = 0; j < batch_details.length; j++)
                {
                    // console.log(brand_details[j].warehouse_id);
                    $("#update_select_inventory").append("<option value='"+batch_details[j].vendor_product_id+"'>"+batch_details[j].batch_name+"</option>");
                }
                $('#update_select_inventory').selectpicker('refresh');
                $('#update_select_inventory').selectpicker('val', order_details[0].vendor_product_id);
            }
            else if(product_type == "Sale")
            {
                $('#div_inventory_rental').hide();
                $('#div_batch_rental').hide();

                $('#div_inventory_sale').show();
                $('#div_batch_sale').show();
            }
        }
    });
}

$("#add_equipment").on("click",function(){
    $("#modal_add_product_details").modal("show");
});