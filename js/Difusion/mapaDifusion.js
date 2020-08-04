export function initMapaGraph(data) {
    // grafico resultados

    var max = data['VILLAMARIA'];
    for (const prop in data) {
        if (data[prop] > max) {
            max = data[prop];
        }
    }

    var width = 600,
        height = 500,
        centered;

    var tooltip = d3.select("body").append("div")
        .attr("class", "tooltip")
        .style("opacity", 0);

    var color = d3.scale.linear()
        .domain([0, max])
        .range(["#A1E5A0", "#07006E"]);
        //.range(["#BCD8C5", "#07006E"]);

    var projection = d3.geo.mercator()
        .scale(19000)
        // Center the Map in Colombia
        .center([-75.4, 5.4])
        .translate([width / 2, height / 2]);

    var path = d3.geo.path()
        .projection(projection);

    // Set svg width & height
    var svg = d3.select('svg#mapa')
        .attr('width', width)
        .attr('height', height);

    // Add background
    svg.append('rect')
        .attr('class', 'background')
        .attr('width', width)
        .attr('height', height)


    var g = svg.append('g');

    var effectLayer = g.append('g')
        .classed('effect-layer', true);

    var mapLayer = g.append('g')
        .classed('map-layer', true);

    var dummyText = g.append('text')
        .classed('dummy-text', true)
        .attr('x', 10)
        .attr('y', 30)
        .style('opacity', 0);

    var bigText = g.append('text')
        .classed('big-text', true)
        .attr('x', 20)
        .attr('y', 45);

    // Load map data
    //d3.json('map2.geojson', function(error, mapData) {
    //d3.json('Colombia.json', function(error, mapData) {

    d3.json('js/map/map2.geojson', function (error, mapData) {
        var features = mapData;
        /*
         features.forEach(function(feature) {
           if(feature.geometry.type == "MultiPolygon") {
             feature.geometry.coordinates.forEach(function(polygon) {
                
               polygon.forEach(function(ring) {
                 ring.reverse();
               })
             })
           }
           else if (feature.geometry.type == "Polygon") {
             feature.geometry.coordinates.forEach(function(ring) {
               ring.reverse();
             })  
           }
         })
        console.log(JSON.stringify(features));*/


        // Draw each province as a path
        mapLayer.selectAll('path')
            .data(features)
            .enter().append('path')
            .attr('d', path)
            .attr("fill", function (d) {

                //var valor = parseInt((Math.floor(Math.random() * 10) + 0));
                return color(data[d.properties.name]);
            })
            .attr("text", function (d) {
                return data[d.properties.name];
            })
            .attr('vector-effect', 'non-scaling-stroke')
            .on("mouseover", function (d) {
                tooltip.transition()
                    .duration(200)
                    .style("opacity", .9);
                tooltip.html(d.properties.name)
                    .style("left", (d3.event.pageX) + "px")
                    .style("top", (d3.event.pageY - 28) + "px");
            })
            .on("mouseout", function (d) {
                tooltip.transition()
                    .duration(200)
                    .style("opacity", .0);
                tooltip.html(d.properties.name)
                    .style("left", (d3.event.pageX) + "px")
                    .style("top", (d3.event.pageY - 28) + "px");
            });
        mapLayer.selectAll("text")
            .data(features)
            .enter()
            .append("svg:text")
            .text(function (d) {
                return data[d.properties.name];
            })
            .attr("x", function (d) {
                return path.centroid(d)[0];
            })
            .attr("y", function (d) {
                return path.centroid(d)[1];
            })
            .style('fill','white')
            .attr("text-anchor", "middle")
            .attr('font-size', '7pt')
            .attr('color', 'black');

        /*.style('fill', fillFn)
        .on('mouseover', mouseover)
        .on('mouseout', mouseout)
        .on('click', clicked);*/
    });


}