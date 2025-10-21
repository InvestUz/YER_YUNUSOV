import json
import ezdxf

# Read GeoJSON file
with open('tashkent_master_plan.geojson', 'r', encoding='utf-8') as f:
    geojson_data = json.load(f)

# Create new DXF document
doc = ezdxf.new('R2010')
msp = doc.modelspace()

# Process each feature
for feature in geojson_data['features']:
    properties = feature['properties']
    geometry = feature['geometry']

    # Get layer name
    layer_name = properties.get('layerName', 'Default')

    # Create layer if it doesn't exist
    if layer_name not in doc.layers:
        doc.layers.add(layer_name)

    # Process geometry
    if geometry['type'] == 'Polygon':
        for ring in geometry['coordinates']:
            # Convert coordinates to 2D points (x, y)
            points = [(coord[0], coord[1]) for coord in ring]

            # Create polyline
            msp.add_lwpolyline(points, close=True, dxfattribs={'layer': layer_name})

    elif geometry['type'] == 'MultiPolygon':
        for polygon in geometry['coordinates']:
            for ring in polygon:
                points = [(coord[0], coord[1]) for coord in ring]
                msp.add_lwpolyline(points, close=True, dxfattribs={'layer': layer_name})

# Save as DXF
doc.saveas('output.dxf')
print("Conversion complete: output.dxf")
