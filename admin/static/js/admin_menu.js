suc_link();
function suc_link(head){
    var html = "";
       
    html += '<div class="WSY_columnnav">';
    html += '<a href="/mshop/admin/index.php?m=weishi&a=weishi_link">知识付费</a>';
    html += '</div>';
    
    $(".WSY_column_header").append(html);
    $(".WSY_column_header").find("a").eq(head).addClass("white1");
}            
                
        